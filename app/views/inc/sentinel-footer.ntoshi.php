<?php /** sentinel SA - Command Centre Footer */ ?>
        </div><!-- .sentinel-content -->

        <!-- Terminal Console -->
        <div style="padding:0.75rem 1.5rem;border-top:1px solid var(--sentinel-glass-border);background:var(--sentinel-surface);">
            <div class="sentinel-terminal" id="sentinel-terminal" data-lines='[]'>
                <div class="terminal-line"><span class="timestamp">[<?= date('H:i:s') ?>]</span> <span class="success">sentinel SA Command Centre initialized</span></div>
                <div class="terminal-line"><span class="timestamp">[<?= date('H:i:s') ?>]</span> <span class="info">System: ACTIVE | Threat Level: <?= $threat_level ?? 'AMBER' ?> | User: <?= esc(user('firstname') . ' ' . user('surname')) ?></span></div>
            </div>
        </div>
    </div><!-- .sentinel-main -->
</div><!-- .sentinel-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= ROOT ?>/assets/js/sentinel/command-centre.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        document.querySelectorAll('.ns-dropdown').forEach(function(dropdown) {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    });
    document.querySelectorAll('.ns-dropdown-anchor').forEach(function(anchor) {
        anchor.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            const dropdown = this.closest('.ns-dropdown');
            document.querySelectorAll('.ns-dropdown.active').forEach(function(openDropdown) {
                if (openDropdown !== dropdown) openDropdown.classList.remove('active');
            });
            dropdown.classList.toggle('active');
        });
    });
});
</script>
</body>
</html>
