/**
 * NtoshiSoft JavaScript Component Library
 * Modern Interactive Components for Neon UI
 *
 * Features: Toast notifications, Modals, Sidebar toggle,
 * Data table enhancements, Form validation feedback, Loading states
 *
 * (c) Jongi Mbodla | Jongi Brands Tech Solutions
 * Part of Ntoshi Framework v1.0
 */

(function() {
  'use strict';

  // ============================================
  // NS (NtoshiSoft) Object
  // ============================================
  window.NS = window.NS || {};

  // ============================================
  // Toast Notification System
  // ============================================
  NS.Toast = {
    container: null,

    init: function() {
      if (!this.container) {
        this.container = document.createElement('div');
        this.container.className = 'ns-toast-container';
        this.container.id = 'ns-toast-container';
        document.body.appendChild(this.container);
      }
    },

    show: function(options) {
      this.init();

      const defaults = {
        type: 'info', // success, error, warning, info
        title: '',
        message: '',
        duration: 5000,
        dismissible: true
      };

      const config = { ...defaults, ...options };

      const icons = {
        success: '<i class="bi bi-check-circle-fill"></i>',
        error: '<i class="bi bi-x-circle-fill"></i>',
        warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
        info: '<i class="bi bi-info-circle-fill"></i>'
      };

      const toast = document.createElement('div');
      toast.className = `ns-toast ns-toast-${config.type}`;
      toast.innerHTML = `
        <div class="ns-toast-icon">${icons[config.type]}</div>
        <div class="ns-toast-content">
          ${config.title ? `<div class="ns-toast-title">${config.title}</div>` : ''}
          <div class="ns-toast-message">${config.message}</div>
        </div>
        ${config.dismissible ? '<button class="ns-toast-close" aria-label="Close">&times;</button>' : ''}
      `;

      // Close button handler
      const closeBtn = toast.querySelector('.ns-toast-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => this.dismiss(toast));
      }

      this.container.appendChild(toast);

      // Auto dismiss
      if (config.duration > 0) {
        setTimeout(() => this.dismiss(toast), config.duration);
      }

      return toast;
    },

    dismiss: function(toast) {
      toast.style.animation = 'ns-slideDown 0.3s ease reverse forwards';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    },

    success: function(message, title = 'Success') {
      return this.show({ type: 'success', title, message });
    },

    error: function(message, title = 'Error') {
      return this.show({ type: 'error', title, message });
    },

    warning: function(message, title = 'Warning') {
      return this.show({ type: 'warning', title, message });
    },

    info: function(message, title = 'Info') {
      return this.show({ type: 'info', title, message });
    }
  };

  // ============================================
  // Modal System
  // ============================================
  NS.Modal = {
    activeModal: null,

    show: function(options) {
      const defaults = {
        title: '',
        content: '',
        footer: '',
        size: 'default', // small, default, large
        closable: true,
        onClose: null
      };

      const config = { ...defaults, ...options };

      // Create backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'ns-modal-backdrop';
      backdrop.id = 'ns-modal-backdrop';

      // Create modal
      const modal = document.createElement('div');
      modal.className = `ns-modal ns-modal-${config.size}`;
      modal.id = 'ns-modal';

      const sizeClasses = {
        small: 'ns-modal-sm',
        default: '',
        large: 'ns-modal-lg'
      };

      if (sizeClasses[config.size]) {
        modal.classList.add(sizeClasses[config.size]);
      }

      modal.innerHTML = `
        <div class="ns-modal-header">
          <h4 class="ns-modal-title">${config.title}</h4>
          ${config.closable ? '<button class="ns-modal-close" aria-label="Close">&times;</button>' : ''}
        </div>
        <div class="ns-modal-body">${config.content}</div>
        ${config.footer ? `<div class="ns-modal-footer">${config.footer}</div>` : ''}
      `;

      // Handle close
      const closeModal = () => this.hide();

      if (config.closable) {
        backdrop.addEventListener('click', closeModal);
        const closeBtn = modal.querySelector('.ns-modal-close');
        if (closeBtn) {
          closeBtn.addEventListener('click', closeModal);
        }
      }

      document.body.appendChild(backdrop);
      document.body.appendChild(modal);

      // Trigger animation
      requestAnimationFrame(() => {
        backdrop.classList.add('active');
        modal.classList.add('active');
      });

      this.activeModal = { modal, backdrop, onClose: config.onClose };

      // Close on ESC key
      const escHandler = (e) => {
        if (e.key === 'Escape' && config.closable) {
          closeModal();
          document.removeEventListener('keydown', escHandler);
        }
      };
      document.addEventListener('keydown', escHandler);

      return modal;
    },

    hide: function() {
      if (this.activeModal) {
        const { modal, backdrop, onClose } = this.activeModal;

        backdrop.classList.remove('active');
        modal.classList.remove('active');

        setTimeout(() => {
          if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
          if (modal.parentNode) modal.parentNode.removeChild(modal);
          if (onClose) onClose();
        }, 300);

        this.activeModal = null;
      }
    },

    confirm: function(options) {
      const defaults = {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: () => {},
        onCancel: () => {}
      };

      const config = { ...defaults, ...options };

      const footer = `
        <button class="ns-btn ns-btn-ghost" id="ns-modal-cancel">${config.cancelText}</button>
        <button class="ns-btn ns-btn-primary" id="ns-modal-confirm">${config.confirmText}</button>
      `;

      const modal = this.show({
        title: config.title,
        content: `<p class="ns-text-center ns-py-lg">${config.message}</p>`,
        footer,
        closable: false
      });

      modal.querySelector('#ns-modal-confirm').addEventListener('click', () => {
        this.hide();
        config.onConfirm();
      });

      modal.querySelector('#ns-modal-cancel').addEventListener('click', () => {
        this.hide();
        config.onCancel();
      });
    }
  };

  // ============================================
  // Sidebar Controller
  // ============================================
  NS.Sidebar = {
    sidebar: null,
    wrapper: null,
    isOpen: true,

    init: function() {
      this.sidebar = document.getElementById('sidebar') || document.querySelector('.ns-sidebar');
      this.wrapper = document.getElementById('wrapper') || document.querySelector('.ns-content-wrapper');

      if (!this.sidebar) return;

      // Create toggle button if not exists
      this.createToggleButton();

      // Handle responsive
      this.handleResponsive();

      // Listen for toggle
      const toggleBtn = document.getElementById('menu-toggle');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', () => this.toggle());
      }

      // Close on outside click (mobile)
      document.addEventListener('click', (e) => {
        if (window.innerWidth <= 1200 &&
            this.sidebar.classList.contains('active') &&
            !this.sidebar.contains(e.target) &&
            !toggleBtn?.contains(e.target)) {
          this.close();
        }
      });
    },

    createToggleButton: function() {
      if (document.getElementById('menu-toggle')) return;

      const toggle = document.createElement('button');
      toggle.id = 'menu-toggle';
      toggle.className = 'ns-sidebar-toggle';
      toggle.innerHTML = '<i class="bi bi-list"></i>';
      toggle.setAttribute('aria-label', 'Toggle sidebar');

      const navbar = document.querySelector('.ns-navbar') || document.querySelector('#page-content-wrapper > nav');
      if (navbar) {
        navbar.insertBefore(toggle, navbar.firstChild);
      }
    },

    toggle: function() {
      if (this.isOpen) {
        this.close();
      } else {
        this.open();
      }
    },

    open: function() {
      if (this.sidebar) {
        this.sidebar.classList.add('active');
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
      }
    },

    close: function() {
      if (this.sidebar) {
        this.sidebar.classList.remove('active');
        this.isOpen = false;
        document.body.style.overflow = '';
      }
    },

    handleResponsive: function() {
      if (window.innerWidth <= 1200) {
        this.close();
      } else {
        this.open();
      }

      window.addEventListener('resize', () => {
        if (window.innerWidth <= 1200) {
          this.close();
        } else {
          this.open();
        }
      });
    }
  };

  // ============================================
  // Data Table Enhancements
  // ============================================
  NS.Table = {
    init: function() {
      const tables = document.querySelectorAll('.ns-table');
      tables.forEach(table => this.enhance(table));
    },

    enhance: function(table) {
      if (!table || table.dataset.nsEnhanced) return;
      table.dataset.nsEnhanced = 'true';

      // Add row hover effect
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.addEventListener('mouseenter', () => {
          row.style.transform = 'translateX(4px)';
          row.style.transition = 'transform 0.2s ease';
        });
        row.addEventListener('mouseleave', () => {
          row.style.transform = 'translateX(0)';
        });
      });
    },

    search: function(tableId, searchTerm) {
      const table = document.getElementById(tableId);
      if (!table) return;

      const rows = table.querySelectorAll('tbody tr');
      searchTerm = searchTerm.toLowerCase();

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    },

    sort: function(tableId, columnIndex) {
      const table = document.getElementById(tableId);
      if (!table) return;

      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));

      const sortedRows = rows.sort((a, b) => {
        const aText = a.querySelectorAll('td')[columnIndex]?.textContent || '';
        const bText = b.querySelectorAll('td')[columnIndex]?.textContent || '';
        return aText.localeCompare(bText);
      });

      sortedRows.forEach(row => tbody.appendChild(row));
    }
  };

  // ============================================
  // Form Validation
  // ============================================
  NS.Form = {
    init: function() {
      const forms = document.querySelectorAll('form');
      forms.forEach(form => this.validate(form));
    },

    validate: function(form) {
      if (!form || form.dataset.nsValidated) return;
      form.dataset.nsValidated = 'true';

      form.addEventListener('submit', (e) => {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            isValid = false;
            this.showFieldError(field, 'This field is required');
          } else {
            this.clearFieldError(field);
          }
        });

        if (!isValid) {
          e.preventDefault();
          NS.Toast.warning('Please fill in all required fields');
        }
      });

      // Real-time validation
      form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', () => {
          if (field.hasAttribute('required') && !field.value.trim()) {
            this.showFieldError(field, 'This field is required');
          } else {
            this.clearFieldError(field);
          }
        });
      });
    },

    showFieldError: function(field, message) {
      field.classList.add('ns-form-error');

      let errorEl = field.parentElement.querySelector('.ns-form-error-msg');
      if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'ns-form-error-msg';
        field.parentElement.appendChild(errorEl);
      }
      errorEl.textContent = message;
    },

    clearFieldError: function(field) {
      field.classList.remove('ns-form-error');
      const errorEl = field.parentElement.querySelector('.ns-form-error-msg');
      if (errorEl) {
        errorEl.remove();
      }
    },

    showLoading: function(form) {
      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="ns-loading-spinner" style="width:20px;height:20px;"></span> Loading...';
      }
    },

    hideLoading: function(form) {
      const submitBtn = form.querySelector('[type="submit"]');
      if (submitBtn && submitBtn.dataset.originalText) {
        submitBtn.disabled = false;
        submitBtn.textContent = submitBtn.dataset.originalText;
      }
    }
  };

  // ============================================
  // Theme Toggle
  // ============================================
  NS.Theme = {
    init: function() {
      const toggle = document.getElementById('theme-toggle');
      if (!toggle) return;

      // Load saved preference
      const saved = localStorage.getItem('ns-theme') || 'dark';
      this.setTheme(saved);

      toggle.addEventListener('change', () => {
        const newTheme = toggle.checked ? 'light' : 'dark';
        this.setTheme(newTheme);
      });
    },

    setTheme: function(theme) {
      document.documentElement.setAttribute('data-bs-theme', theme);
      localStorage.setItem('ns-theme', theme);

      const toggle = document.getElementById('theme-toggle');
      if (toggle) {
        toggle.checked = theme === 'light';
      }
    },

    toggle: function() {
      const current = document.documentElement.getAttribute('data-bs-theme');
      this.setTheme(current === 'dark' ? 'light' : 'dark');
    }
  };

  // ============================================
  // Animate Counter
  // ============================================
  NS.Counter = {
    animate: function(element, target, duration = 2000) {
      if (!element) return;

      const start = 0;
      const startTime = performance.now();

      const update = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Easing function
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.floor(easeOutQuart * target);

        element.textContent = current.toLocaleString();

        if (progress < 1) {
          requestAnimationFrame(update);
        } else {
          element.textContent = target.toLocaleString();
        }
      };

      requestAnimationFrame(update);
    },

    init: function() {
      const counters = document.querySelectorAll('[data-counter]');
      counters.forEach(counter => {
        const target = parseInt(counter.dataset.counter, 10);
        const duration = parseInt(counter.dataset.duration, 10) || 2000;

        // Use Intersection Observer for scroll-triggered animation
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              this.animate(counter, target, duration);
              observer.unobserve(counter);
            }
          });
        }, { threshold: 0.5 });

        observer.observe(counter);
      });
    }
  };

  // ============================================
  // Image Preview
  // ============================================
  NS.ImagePreview = {
    init: function() {
      const inputs = document.querySelectorAll('input[type="file"]');
      inputs.forEach(input => {
        if (input.dataset.nsImagePreview) return;
        input.dataset.nsImagePreview = 'true';

        input.addEventListener('change', (e) => {
          const file = e.target.files[0];
          if (!file) return;

          const reader = new FileReader();
          const preview = input.closest('label')?.querySelector('img') ||
                         input.parentElement.querySelector('img');

          if (preview) {
            reader.onload = (e) => {
              preview.src = e.target.result;
              preview.style.transform = 'scale(1.1)';
              setTimeout(() => preview.style.transform = 'scale(1)', 300);
            };
            reader.readAsDataURL(file);
          }
        });
      });
    }
  };

  // ============================================
  // Delete Confirmation
  // ============================================
  NS.DeleteConfirm = {
    init: function() {
      const links = document.querySelectorAll('a[href*="delete"], a[data-delete]');
      links.forEach(link => {
        if (link.dataset.nsDeleteInit) return;
        link.dataset.nsDeleteInit = 'true';

        link.addEventListener('click', (e) => {
          e.preventDefault();
          const url = link.getAttribute('href');
          const message = link.dataset.deleteMessage || 'Are you sure you want to delete this item? This action cannot be reversed.';

          NS.Modal.confirm({
            title: 'Confirm Delete',
            message: message,
            confirmText: 'Delete',
            cancelText: 'Cancel',
            onConfirm: () => {
              window.location.href = url;
            }
          });
        });
      });
    }
  };

  // ============================================
  // Smooth Scroll
  // ============================================
  NS.SmoothScroll = {
    init: function() {
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
          const targetId = anchor.getAttribute('href');
          if (targetId === '#') return;

          const target = document.querySelector(targetId);
          if (target) {
            e.preventDefault();
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });
    }
  };

  // ============================================
  // Staggered Animation
  // ============================================
  NS.StaggerAnimation = {
    init: function() {
      const elements = document.querySelectorAll('[data-stagger]');
      elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
        el.classList.add('ns-animate-slideUp');
      });
    }
  };

  // ============================================
  // Flash Message Display
  // ============================================
  NS.Flash = {
    init: function() {
      const flashMessages = document.querySelectorAll('[data-flash]');
      flashMessages.forEach(msg => {
        const type = msg.dataset.flash || 'info';
        const message = msg.textContent;

        NS.Toast.show({
          type: type,
          message: message,
          duration: 5000
        });

        msg.remove();
      });
    }
  };

  // ============================================
  // Initialize All Components
  // ============================================
  NS.init = function() {
    document.addEventListener('DOMContentLoaded', () => {
      this.Sidebar.init();
      this.Table.init();
      this.Form.init();
      this.Theme.init();
      this.Counter.init();
      this.ImagePreview.init();
      this.DeleteConfirm.init();
      this.SmoothScroll.init();
      this.StaggerAnimation.init();
      this.Flash.init();

      console.log('NtoshiSoft UI initialized');
    });
  };

  // Run initialization
  NS.init();

})();