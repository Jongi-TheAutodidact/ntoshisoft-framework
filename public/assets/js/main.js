/*!
 * Ntoshi PHP MVC Framework - Frontend Stylesheet
 * (c) Jongi Mbodla | Jongi Brands Tech Solutions
 * Website: https://jongibrandz.co.za
 *
 * This file is part of the Ntoshi Framework.
 * All rights reserved. Unauthorized use, reproduction, or distribution is strictly prohibited.
 *
 * Released as part of the GPL-3.0-or-later license.
 */

// In your existing <script> (or style.js)
$(document).ready(function () {
  // --- Theme Toggle ---
  const themeToggle = $('#theme-toggle');
  if (!localStorage.getItem('theme')) localStorage.setItem('theme','dark');
  $('html').attr('data-bs-theme','dark');
  themeToggle.prop('checked', true);

  themeToggle.on('change', function () {
    if (this.checked) {
      $('html').attr('data-bs-theme','dark');
      localStorage.setItem('theme','dark');
    } else {
      $('html').attr('data-bs-theme','light');
      localStorage.setItem('theme','light');
    }
  });

  // --- Sidebar Toggle ---
  $('#menu-toggle').click(function (e) {
    e.preventDefault();
    $('#wrapper').toggleClass('toggled');
    $(this).toggleClass('bi-list bi-x-lg');
  });

  // --- Sidebar Active Link Management ---
  function setActiveSidebarLink () {
    let currentPageFile = (
      window.location.pathname.split('/').pop() || 'index'
    )
      .split('?')[0]
      .split('#')[0]

    $('.sidebar-wrapper .components > li').removeClass('active')
    $('.sidebar-wrapper .nav-link')
      .removeClass('active')
      .attr('aria-expanded', 'false')
    $('.sidebar-wrapper .collapse').removeClass('show')

    // Handle special case for single-customer to activate 'All Customers' or parent 'Customers'
    if (currentPageFile === 'single-customer') {
      currentPageFile = 'customers' // Treat as 'customers' for sidebar highlighting
    }

    let linkFound = false
    $('.sidebar-wrapper .nav-link').each(function () {
      const $link = $(this)
      let linkTarget = $link.attr('href').split('?')[0].split('#')[0]

      if (linkTarget === '#') {
        // For dropdown toggles or placeholder links, check their sub-menu items if expanded.
        // Or, rely on the logic that one of its children will match.
        // This part is primarily for direct page links.
        linkTarget = 'index' // Assuming '#' for dashboard might be 'index'
      } else {
        linkTarget = linkTarget.split('/').pop()
      }

      if (linkTarget === currentPageFile) {
        $link.addClass('active')
        $link.closest('li').addClass('active')

        const parentCollapse = $link.closest('.collapse')
        if (parentCollapse.length) {
          parentCollapse.addClass('show')
          const dropdownToggle = parentCollapse.prev(
            '.dropdown-toggle.nav-link'
          )
          if (dropdownToggle.length) {
            dropdownToggle.addClass('active').attr('aria-expanded', 'true')
            dropdownToggle.closest('li').addClass('active')
          }
        }
        linkFound = true
        return false
      }
    })

    if (!linkFound && currentPageFile === 'index') {
      const dashboardLink = $(
        '.sidebar-wrapper .nav-link[href="index"], .sidebar-wrapper .nav-link[href="#"]'
      ).first()
      if (dashboardLink.length) {
        dashboardLink.addClass('active')
        dashboardLink.closest('li').addClass('active')
      }
    }
  }
  setActiveSidebarLink()

  $('.sidebar-wrapper .nav-link.dropdown-toggle').on('click', function (e) {
    // Bootstrap handles collapse/expand. If navigating, setActiveSidebarLink will run on new page.
    // This event could be used for more complex SPA-like behavior if Bootstrap's default toggle is prevented.
  })

  

  // Age Calculation
  function calculateAge () {
    var dob = document.getElementById('dob').value
    var today = new Date()
    var birthDate = new Date(dob)
    var age = today.getFullYear() - birthDate.getFullYear()
    var m = today.getMonth() - birthDate.getMonth()
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--
    }
    document.getElementById('age').value = age
  }
  
})




