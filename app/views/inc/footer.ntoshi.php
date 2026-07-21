<?php /** No view variables needed - uses only constants/Session */ ?>
</main>
<footer class="footer">
    <div class="container">
        <p style="font-size: 12px;color:#c8b27b"> &copy; <?= date('Y') . ' ' . APP_NAME ?> – by <a href="https://jongibrandz.co.za" target="_blank">Jongi Brands (Pty) Ltd - CIPC Reg. No. 2019/064124/07</a> <br> powered by <a href="https://techsolutions.jongibrandz.co.za"><span class="text-warning">Jongi Brands Tech Solutions</span></a> | Digital Sovereignty Movement <br>
            <a href="<?= ROOT . '/privacy' ?>">Privacy Policy</a> | <a href="<?= ROOT . '/popia' ?>">POPIA Compliance</a> | <a href="mailto:jongim@jongibrandz.co.za">Contact</a>
        </p>
    </div>
</footer>

<script src="<?= ROOT . '/assets/js/bootstrap.bundle.min.js' ?>"></script>
<!-- JB Voice Contact Widget -->
<script src="https://jb-voice-contact.jongibrandz.co.za/assets/js/jb-voice-widget.js"></script>
<script>
    function createStars() {
        const starfield = document.getElementById('starfield');
        if (!starfield) return;
        const starCount = 200;
        for (let i = 0; i < starCount; i++) {
            let star = document.createElement('div');
            star.classList.add('star');
            let size = Math.random() * 3 + 1;
            star.style.width = size + 'px';
            star.style.height = size + 'px';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDuration = Math.random() * 30 + 10 + 's';
            star.style.animationDelay = Math.random() * 20 + 's';
            starfield.appendChild(star);
        }
    }
    createStars();

    const toggle = document.getElementById('themeToggle');
    const body = document.body;
    const themeIcon = document.getElementById('themeIcon');
    const themeLabel = document.getElementById('themeLabel');
    let isDark = true;

    toggle.addEventListener('click', () => {
        if (isDark) {
            body.classList.add('light');
            themeIcon.className = 'fas fa-moon';
            themeLabel.innerText = 'Dark';
        } else {
            body.classList.remove('light');
            themeIcon.className = 'fas fa-sun';
            themeLabel.innerText = 'Light';
        }
        isDark = !isDark;
    });

    // Add these to your main global JavaScript file

    // Share functions
    window.shareOnLinkedIn = function(url, title) {
        url = url || window.location.href;
        window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank');
    };

    window.shareOnTwitter = function(url, title) {
        url = url || window.location.href;
        title = title || document.title;
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`, '_blank');
    };

    window.shareOnWhatsApp = function(text) {
        text = text || document.title + ' ' + window.location.href;
        window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank');
    };

    window.copyToClipboard = function(text) {
        text = text || window.location.href;
        navigator.clipboard.writeText(text);
        // Use your existing notification system instead of alert
        showNotification('Link copied to clipboard!', 'success');
    };

    // Scroll to element function
    window.scrollToElement = function(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth'
            });
        }
    };

    /* ============================================
   DROPDOWN MENU - JavaScript
   ============================================ */

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.ns-dropdown');

        dropdowns.forEach(function(dropdown) {
            // If click is outside the dropdown
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    });

    // Toggle dropdown on anchor click
    document.querySelectorAll('.ns-dropdown-anchor').forEach(function(anchor) {
        anchor.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();

            const dropdown = this.closest('.ns-dropdown');

            // Close other open dropdowns
            document.querySelectorAll('.ns-dropdown.active').forEach(function(openDropdown) {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('active');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('active');
        });
    });

    // Close dropdown when an item is clicked
    document.querySelectorAll('.ns-dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(event) {
            const dropdown = this.closest('.ns-dropdown');

            if (dropdown) {
                dropdown.classList.remove('active');
            }
        });
    });

    // Optional: Close dropdown when Escape key is pressed
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.ns-dropdown.active').forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        }
    });
</script>
</body>

</html>