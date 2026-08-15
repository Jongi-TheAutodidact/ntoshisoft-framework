/*!
 * NtoshiSoft Service Worker — Offline-First runtime cache.
 * (c) Jongi Mbodla | Jongi Brands Tech Solutions
 *
 * Part of the NtoshiSoft framework offline-first / PWA feature.
 *
 * Strategy:
 *  - Static assets (/assets/*) : cache-first
 *  - Page navigations            : network-first, fall back to the last cached page
 *  - /offline/* sync API         : network-only (carries per-user data)
 *  - Cross-origin (CDNs etc.)    : never intercepted
 */

'use strict';

const VERSION = 'v1.0.0';
const CACHE_STATIC = 'ntoshi-static-' + VERSION;
const CACHE_PAGES = 'ntoshi-pages-' + VERSION;

// URLs are resolved relative to this worker's location (public/ web root).
const PRECACHE_URLS = [
  'assets/css/style.css',
  'assets/css/bootstrap.min.css',
  'assets/css/offline.css',
  'assets/js/bootstrap.bundle.min.js',
  'assets/js/ntoshi-js.js',
  'assets/js/offline.js',
  'assets/img/logos/logo.png',
  'assets/img/logos/favicon.png',
  'manifest.json'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(CACHE_STATIC)
      // allSettled keeps install alive even if one optional URL fails.
      .then((cache) => Promise.allSettled(PRECACHE_URLS.map((url) => cache.add(url))))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches
      .keys()
      .then((keys) =>
        Promise.all(
          keys
            .filter(
              (key) =>
                (key.startsWith('ntoshi-static-') && key !== CACHE_STATIC) ||
                (key.startsWith('ntoshi-pages-') && key !== CACHE_PAGES)
            )
            .map((key) => caches.delete(key))
        )
      )
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return;

  // The sync API is network-only — never cache per-user data.
  if (url.pathname.includes('/offline/')) return;

  if (url.pathname.includes('/assets/')) {
    event.respondWith(cacheFirst(request));
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(networkFirst(request, true));
    return;
  }

  event.respondWith(networkFirst(request, false));
});

async function cacheFirst(request) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response && response.ok) {
      const cache = await caches.open(CACHE_STATIC);
      cache.put(request, response.clone());
    }
    return response;
  } catch (err) {
    return new Response('', { status: 503, statusText: 'Offline' });
  }
}

async function networkFirst(request, isPage) {
  const cacheName = isPage ? CACHE_PAGES : CACHE_STATIC;
  try {
    const response = await fetch(request);
    if (response && response.ok) {
      const cache = await caches.open(cacheName);
      cache.put(request, response.clone());
    }
    return response;
  } catch (err) {
    const cached = await caches.match(request);
    if (cached) return cached;

    if (isPage) {
      // Last resort: serve the cached home page so the app shell stays usable.
      const home = await caches.match('./');
      if (home) return home;
    }

    return new Response('You are offline and this page has not been cached yet.', {
      status: 503,
      headers: { 'Content-Type': 'text/plain' }
    });
  }
}
