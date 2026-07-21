/* ============================================
   sentinel SA - COMMAND CENTRE JAVASCRIPT
   Community Intelligence & Situational Awareness
   ============================================ */

const sentinel = (function() {

  // ============================================
  // STATE
  // ============================================
  const state = {
    sidebarOpen: window.innerWidth > 992,
    theme: 'dark',
    terminalLines: [],
    feedItems: [],
    notifications: [],
    activeNav: ''
  };

  // ============================================
  // SIDEBAR
  // ============================================
  function initSidebar() {
    const toggleBtn = document.getElementById('sentinel-toggle');
    const sidebar = document.getElementById('sentinel-sidebar');
    const overlay = document.getElementById('sentinel-overlay');

    if (!sidebar) return;

    if (window.innerWidth <= 992) {
      state.sidebarOpen = false;
      sidebar.classList.remove('open');
      if (overlay) overlay.classList.remove('open');
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        state.sidebarOpen = !state.sidebarOpen;
        sidebar.classList.toggle('open', state.sidebarOpen);
        if (overlay) overlay.classList.toggle('open', state.sidebarOpen);
      });
    }

    if (overlay) {
      overlay.addEventListener('click', function() {
        state.sidebarOpen = false;
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
      });
    }

    // Highlight active nav
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sentinel-nav-item').forEach(function(item) {
      const href = item.getAttribute('href');
      if (href && currentPath.includes(href)) {
        item.classList.add('active');
      }
    });
  }

  // ============================================
  // TERMINAL CONSOLE
  // ============================================
  function initTerminal(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const lines = container.getAttribute('data-lines');
    if (lines) {
      try {
        const parsed = JSON.parse(lines);
        parsed.forEach(function(line) {
          addTerminalLine(line.text, line.type || 'info');
        });
      } catch(e) {}
    }

    // Auto-scroll to bottom
    container.scrollTop = container.scrollHeight;
  }

  function addTerminalLine(text, type) {
    const container = document.querySelector('.sentinel-terminal');
    if (!container) return;

    const line = document.createElement('div');
    line.className = 'terminal-line';
    const ts = new Date().toLocaleTimeString('en-ZA', { hour12: false });
    line.innerHTML = '<span class="timestamp">[' + ts + ']</span> ' +
      '<span class="' + type + '">' + escapeHtml(text) + '</span>';
    container.appendChild(line);
    container.scrollTop = container.scrollHeight;
  }

  // ============================================
  // LIVE FEED
  // ============================================
  function addFeedItem(item) {
    const container = document.getElementById('sentinel-feed');
    if (!container) return;

    const colors = {
      incident: { bg: 'rgba(239,68,68,0.15)', icon: 'fa-shield-halved', color: '#ef4444' },
      observation: { bg: 'rgba(59,130,246,0.15)', icon: 'fa-eye', color: '#3b82f6' },
      alert: { bg: 'rgba(245,158,11,0.15)', icon: 'fa-bell', color: '#f59e0b' },
      intel: { bg: 'rgba(139,92,246,0.15)', icon: 'fa-brain', color: '#8b5cf6' },
      task: { bg: 'rgba(16,185,129,0.15)', icon: 'fa-list-check', color: '#10b981' }
    };

    const cfg = colors[item.type] || colors.intel;

    const el = document.createElement('div');
    el.className = 'sentinel-feed-item ' + (item.type || 'intel');
    el.innerHTML =
      '<div class="sentinel-feed-icon" style="background:' + cfg.bg + ';color:' + cfg.color + '">' +
        '<i class="fa-solid ' + cfg.icon + '"></i>' +
      '</div>' +
      '<div class="sentinel-feed-content">' +
        '<div class="feed-title">' + escapeHtml(item.title) + '</div>' +
        (item.meta ? '<div class="feed-meta">' + escapeHtml(item.meta) + '</div>' : '') +
      '</div>' +
      '<div class="sentinel-feed-time">' + (item.time || 'now') + '</div>';

    container.insertBefore(el, container.firstChild);

    // Keep max 50 items
    while (container.children.length > 50) {
      container.removeChild(container.lastChild);
    }
  }

  // ============================================
  // SEARCH TABLE
  // ============================================
  function initTableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    input.addEventListener('keyup', function() {
      const query = this.value.toLowerCase();
      const rows = table.querySelectorAll('tbody tr');

      rows.forEach(function(row) {
        let found = false;
        row.querySelectorAll('td').forEach(function(cell) {
          if (cell.textContent.toLowerCase().includes(query)) {
            found = true;
          }
        });
        row.style.display = found ? '' : 'none';
      });
    });
  }

  // ============================================
  // CONFIRM DIALOG
  // ============================================
  function confirmAction(message, callback) {
    if (confirm(message || 'Are you sure you want to proceed?')) {
      if (typeof callback === 'function') callback();
    }
  }

  // ============================================
  // NOTIFICATION TOAST
  // ============================================
  function showNotification(message, type) {
    type = type || 'info';
    const container = document.getElementById('sentinel-toasts');
    if (!container) {
      // Create toast container
      const div = document.createElement('div');
      div.id = 'sentinel-toasts';
      div.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;';
      document.body.appendChild(div);
    }

    const colors = {
      success: { bg: 'rgba(16,185,129,0.1)', border: 'rgba(16,185,129,0.3)', color: '#10b981', icon: 'fa-check-circle' },
      error: { bg: 'rgba(239,68,68,0.1)', border: 'rgba(239,68,68,0.3)', color: '#ef4444', icon: 'fa-times-circle' },
      warning: { bg: 'rgba(245,158,11,0.1)', border: 'rgba(245,158,11,0.3)', color: '#f59e0b', icon: 'fa-exclamation-circle' },
      info: { bg: 'rgba(59,130,246,0.1)', border: 'rgba(59,130,246,0.3)', color: '#3b82f6', icon: 'fa-info-circle' }
    };

    const cfg = colors[type] || colors.info;
    const toast = document.createElement('div');
    toast.style.cssText = 'display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border-radius:8px;background:' + cfg.bg + ';border:1px solid ' + cfg.border + ';color:' + cfg.color + ';font-size:0.85rem;min-width:280px;max-width:400px;box-shadow:0 4px 20px rgba(0,0,0,0.3);animation:alert-slide 0.3s ease;backdrop-filter:blur(12px);';
    toast.innerHTML = '<i class="fa-solid ' + cfg.icon + '"></i> ' + escapeHtml(message);

    document.getElementById('sentinel-toasts').appendChild(toast);

    setTimeout(function() {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s ease';
      setTimeout(function() { toast.remove(); }, 300);
    }, 4000);
  }

  // ============================================
  // UTILITY
  // ============================================
  function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ============================================
  // INIT
  // ============================================
  function init() {
    initSidebar();

    const terminal = document.querySelector('.sentinel-terminal');
    if (terminal) initTerminal(terminal.id);

    // Auto-init table searches
    document.querySelectorAll('[data-sentinel-search]').forEach(function(input) {
      const tableId = input.getAttribute('data-sentinel-search');
      initTableSearch(input.id, tableId);
    });

    // Listen for custom feed events
    document.addEventListener('sentinel:feed', function(e) {
      if (e.detail) addFeedItem(e.detail);
    });

    document.addEventListener('sentinel:terminal', function(e) {
      if (e.detail) addTerminalLine(e.detail.text, e.detail.type);
    });

    document.addEventListener('sentinel:notify', function(e) {
      if (e.detail) showNotification(e.detail.message, e.detail.type);
    });

    // Log startup
    addTerminalLine('sentinel SA Command Centre initialized', 'success');
    addTerminalLine('System status: ALL SYSTEMS OPERATIONAL', 'info');
    addTerminalLine('Session established: ' + new Date().toISOString(), 'info');
  }

  // Public API
  return {
    init: init,
    addFeedItem: addFeedItem,
    addTerminalLine: addTerminalLine,
    showNotification: showNotification,
    confirmAction: confirmAction,
    initTableSearch: initTableSearch,
    escapeHtml: escapeHtml
  };

})();

document.addEventListener('DOMContentLoaded', function() {
  sentinel.init();
});
