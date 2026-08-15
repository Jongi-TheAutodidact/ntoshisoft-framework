/*!
 * NtoshiSoft Offline Engine — IndexedDB local store + sync queue.
 * (c) Jongi Mbodla | Jongi Brands Tech Solutions
 *
 * Part of the NtoshiSoft framework offline-first / PWA feature.
 *
 * What this does:
 *  1. Reads the app's offline config from /offline/config (which tables are
 *     offline-capable, sync interval).
 *  2. Caches rows of those tables in IndexedDB so the app keeps working without
 *     an internet connection.
 *  3. Intercepts <form data-offline-table="..."> submissions made while offline
 *     and queues them locally instead of losing the data.
 *  4. When the connection returns, pushes the queue to /offline/push and pulls
 *     fresh data for every offline table.
 *  5. Shows a small status pill (online / offline / pending sync / syncing).
 *
 * Requirements on the page:
 *  - window.NTOSHI_ROOT (set by the framework's footer) — app base URL.
 *  - <meta name="csrf-token"> (set by the framework's header) — CSRF for push.
 *  - <body data-user="..."> — present only when a user is logged in.
 */

(function (global) {
  'use strict';

  var DB_NAME = 'ntoshisoft-offline';
  var DB_VERSION = 1;
  var STORE_CACHE = 'cache';
  var STORE_QUEUE = 'queue';
  var STORE_META = 'meta';

  var dbPromise = null;
  var config = { enabled: false, interval: 30, tables: [] };
  var syncing = false;
  var statusEl = null;

  var root = global.NTOSHI_ROOT || '';
  var api = function (path) { return root + path; };

  // ------------------------------------------------ IndexedDB helpers
  function openDB() {
    if (dbPromise) return dbPromise;
    dbPromise = new Promise(function (resolve, reject) {
      if (!('indexedDB' in global)) {
        reject(new Error('IndexedDB is not supported in this browser.'));
        return;
      }
      var req = indexedDB.open(DB_NAME, DB_VERSION);
      req.onupgradeneeded = function (e) {
        var db = e.target.result;
        if (!db.objectStoreNames.contains(STORE_CACHE)) {
          var cache = db.createObjectStore(STORE_CACHE, { keyPath: 'id' });
          cache.createIndex('table', 'table', { unique: false });
        }
        if (!db.objectStoreNames.contains(STORE_QUEUE)) {
          db.createObjectStore(STORE_QUEUE, { keyPath: 'uuid' });
        }
        if (!db.objectStoreNames.contains(STORE_META)) {
          db.createObjectStore(STORE_META, { keyPath: 'key' });
        }
      };
      req.onsuccess = function () { resolve(req.result); };
      req.onerror = function () { reject(req.error); };
    });
    return dbPromise;
  }

  function tx(storeName, mode, fn) {
    return openDB().then(function (db) {
      return new Promise(function (resolve, reject) {
        var t = db.transaction(storeName, mode);
        var store = t.objectStore(storeName);
        var request = fn(store);
        t.oncomplete = function () { resolve(request ? request.result : undefined); };
        t.onerror = function () { reject(t.error); };
        t.onabort = function () { reject(t.error); };
      });
    });
  }

  function put(storeName, value) {
    return tx(storeName, 'readwrite', function (store) { return store.put(value); });
  }

  function del(storeName, key) {
    return tx(storeName, 'readwrite', function (store) { return store.delete(key); });
  }

  function getAll(storeName) {
    return tx(storeName, 'readonly', function (store) { return store.getAll(); });
  }

  function getMeta(key, fallback) {
    return tx(STORE_META, 'readonly', function (store) { return store.get(key); }).then(function (r) {
      return r ? r.value : fallback;
    });
  }

  function setMeta(key, value) {
    return put(STORE_META, { key: key, value: value });
  }

  // ------------------------------------------------ helpers
  function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function isAuthenticated() {
    return !!(document.body && document.body.dataset && document.body.dataset.user);
  }

  function newUuid() {
    if (global.crypto && global.crypto.randomUUID) return global.crypto.randomUUID();
    return 'id-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12);
  }

  function keyFor(table) {
    var found = null;
    config.tables.forEach(function (t) { if (t.table === table) found = t; });
    return found ? found.key : 'id';
  }

  function toast(message, type) {
    if (global.NS && global.NS.Toast) {
      if (type === 'error') global.NS.Toast.error(message);
      else global.NS.Toast.success(message);
    } else if (global.alert) {
      global.alert(message);
    }
  }

  // ------------------------------------------------ config / discovery
  function loadConfig() {
    return fetch(api('/offline/config'), {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('config failed');
        return res.json();
      })
      .then(function (json) {
        if (json && json.success) {
          config.enabled = !!json.offline.enabled;
          config.interval = parseInt(json.offline.interval, 10) || 30;
          config.tables = Array.isArray(json.tables) ? json.tables : [];
        }
        return config;
      })
      .catch(function () {
        config.enabled = false;
        return config;
      });
  }

  // ------------------------------------------------ queue
  function queue(table, action, data, id) {
    var item = {
      uuid: newUuid(),
      table: table,
      action: action,
      id: id !== undefined && id !== null && id !== '' ? id : null,
      data: data || {},
      queued_at: new Date().toISOString()
    };
    return put(STORE_QUEUE, item).then(function () {
      updateStatus();
      if (navigator.onLine) return sync();
      return false;
    }).then(function () { return item; });
  }

  function pendingCount() {
    return getAll(STORE_QUEUE).then(function (items) { return items.length; });
  }

  // ------------------------------------------------ sync
  function sync() {
    if (syncing || !config.enabled || !navigator.onLine || !isAuthenticated()) {
      return Promise.resolve(false);
    }
    syncing = true;
    setSyncing(true);

    return getAll(STORE_QUEUE)
      .then(function (items) {
        if (!items.length) return null;
        return fetch(api('/offline/push'), {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
          },
          body: JSON.stringify({ items: items })
        }).then(function (res) { return { res: res, items: items }; });
      })
      .then(function (payload) {
        if (!payload) return false;

        // Session or CSRF problem — keep the queue and retry later.
        if (payload.res.status === 401 || payload.res.status === 403) return false;

        return payload.res.json().then(function (json) {
          if (!json || !json.success || !Array.isArray(json.results)) return false;

          var removals = [];
          var mappings = {};

          json.results.forEach(function (r) {
            if (!r.success) return;
            removals.push(del(STORE_QUEUE, r.uuid));
            if (r.id) {
              var item = null;
              payload.items.forEach(function (i) { if (i.uuid === r.uuid) item = i; });
              if (item) {
                if (!mappings[item.table]) mappings[item.table] = [];
                mappings[item.table].push({ from: item.id, to: r.id });
              }
            }
          });

          return Promise.all(removals).then(function () { return rememberIdMappings(mappings); });
        });
      })
      .then(function () { return pullAll(); })
      .catch(function () { return false; })
      .then(function (r) {
        syncing = false;
        setSyncing(false);
        updateStatus();
        return r;
      });
  }

  function rememberIdMappings(mappings) {
    var keys = Object.keys(mappings);
    if (!keys.length) return Promise.resolve();
    return getMeta('idmap', {}).then(function (current) {
      keys.forEach(function (table) {
        if (!current[table]) current[table] = [];
        mappings[table].forEach(function (m) { current[table].push(m); });
      });
      return setMeta('idmap', current);
    });
  }

  function pullAll() {
    var tables = [];
    config.tables.forEach(function (t) { tables.push(t.table); });
    return Promise.all(tables.map(function (table) { return pull(table); }));
  }

  function pull(table) {
    return fetch(api('/offline/pull/' + encodeURIComponent(table)), {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    })
      .then(function (res) {
        if (!res.ok) throw new Error('pull failed for ' + table);
        return res.json();
      })
      .then(function (json) {
        if (json && json.success && Array.isArray(json.rows)) {
          var key = keyFor(table);
          var writes = json.rows.map(function (row) {
            return put(STORE_CACHE, {
              id: String(row[key]),
              table: table,
              data: row,
              updated_at: Date.now()
            });
          });
          return Promise.all(writes).then(function () {
            return setMeta('last_pull_' + table, Date.now());
          });
        }
        return null;
      })
      .catch(function () { return null; });
  }

  // ------------------------------------------------ cache reads
  function getCached(table, id) {
    return tx(STORE_CACHE, 'readonly', function (store) { return store.get(String(id)); }).then(function (rec) {
      return rec && rec.table === table ? rec.data : null;
    });
  }

  function allCached(table) {
    return tx(STORE_CACHE, 'readonly', function (store) { return store.index('table').getAll(table); });
  }

  // ------------------------------------------------ offline forms
  function inferAction(form, hasId) {
    var action = (form.getAttribute('action') || '').toLowerCase();
    if (action.indexOf('delete') !== -1) return 'delete';
    if (action.indexOf('edit') !== -1 || hasId) return 'update';
    return 'insert';
  }

  function initForms() {
    var forms = document.querySelectorAll('form[data-offline-table]');
    forms.forEach(function (form) {
      if (form.dataset.offlineInit === '1') return;
      form.dataset.offlineInit = '1';

      form.addEventListener('submit', function (e) {
        var force = form.dataset.offlineQueue === 'always';
        if (navigator.onLine && !force) return; // online → normal server submit

        e.preventDefault();

        var table = form.dataset.offlineTable;
        var rawId = form.dataset.offlineId || '';
        var action = form.dataset.offlineAction || inferAction(form, !!rawId);
        var fd = new FormData(form);
        var data = {};
        fd.forEach(function (value, key) { data[key] = value; });

        queue(table, action, data, rawId || null)
          .then(function () {
            toast('Saved offline — it will sync automatically.', 'success');
          })
          .catch(function () {
            toast('Could not save offline. Please try again.', 'error');
          });
      });
    });
  }

  // ------------------------------------------------ status UI
  function buildUI() {
    statusEl = document.getElementById('ntoshi-sync-status');
    if (statusEl) return;

    statusEl = document.createElement('div');
    statusEl.id = 'ntoshi-sync-status';
    statusEl.className = 'ntoshi-sync-status is-offline';
    statusEl.setAttribute('role', 'status');
    statusEl.innerHTML =
      '<span class="ntoshi-sync-dot"></span>' +
      '<span class="ntoshi-sync-label">Offline</span>';
    statusEl.title = 'Click to sync now';
    document.body.appendChild(statusEl);

    statusEl.addEventListener('click', function () { sync(); });
  }

  function setSyncing(value) {
    if (statusEl) statusEl.classList.toggle('is-syncing', value);
  }

  function updateStatus() {
    if (!statusEl) return;
    pendingCount()
      .then(function (n) {
        var online = navigator.onLine;
        statusEl.classList.toggle('is-online', online);
        statusEl.classList.toggle('is-offline', !online);
        statusEl.classList.toggle('is-syncing', syncing);

        var label = statusEl.querySelector('.ntoshi-sync-label');
        if (!config.enabled || !isAuthenticated()) {
          label.textContent = 'Offline';
        } else if (syncing) {
          label.textContent = 'Syncing\u2026';
        } else if (online) {
          label.textContent = n ? n + ' pending sync' : 'Online';
        } else {
          label.textContent = 'Offline \u2014 ' + n + ' queued';
        }
      })
      .catch(function () {});
  }

  // ------------------------------------------------ init
  function init() {
    buildUI();
    openDB().catch(function () {});

    loadConfig().then(function () {
      updateStatus();
      if (!config.enabled) return;

      initForms();

      // Pull fresh data, then push anything still queued.
      pullAll().then(function () { sync(); });

      if (config.interval > 0) {
        setInterval(function () { sync(); }, config.interval * 1000);
      }
    });

    global.addEventListener('online', function () {
      updateStatus();
      sync();
    });
    global.addEventListener('offline', function () {
      updateStatus();
    });
  }

  // Auto-start after the DOM is ready.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  global.NtoshiOffline = {
    init: init,
    queue: queue,
    sync: sync,
    pull: pull,
    getCached: getCached,
    allCached: allCached,
    pendingCount: pendingCount,
    isEnabled: function () { return config.enabled; },
    isOnline: function () { return navigator.onLine; }
  };
})(window);
