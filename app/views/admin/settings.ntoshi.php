<?php
/**
 * @var array $settings
 * @var string $logo_url
 */
$this->view('inc/header', $data);
?>

<div class="glass-card">
    <h2 class="gradient-text mb-4"><i class="fas fa-cog"></i> System Settings</h2>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <form method="post" action="<?= ROOT ?>/admin/settings/update" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Branding -->
            <div class="col-12">
                <h5 style="color: var(--primary); border-bottom: 1px solid rgba(213,186,11,0.2); padding-bottom: 0.5rem;">
                    <i class="fas fa-palette"></i> Branding
                </h5>
            </div>
            <div class="col-md-6">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= esc($settings['site_name'] ?? 'NtoshiSoft  Form') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Primary Color</label>
                <input type="color" name="primary_color" class="form-control form-control-color" value="<?= esc($settings['primary_color'] ?? '#d5ba0b') ?>" style="padding:0.25rem;height:42px;">
            </div>
            <div class="col-12">
                <label class="form-label">Logo</label>
                <div class="d-flex align-items-center gap-3">
                    <div style="width:120px;height:60px;background:rgba(0,0,0,0.2);border-radius:0.5rem;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <img src="<?= $logo_url ?>" alt="Logo" style="max-width:100%;max-height:100%;object-fit:contain;">
                    </div>
                    <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml">
                </div>
                <small class="text-muted">Upload a new logo to replace the current one. Recommended: transparent PNG or SVG.</small>
            </div>

            <!-- SEO -->
            <div class="col-12 mt-3">
                <h5 style="color: var(--primary); border-bottom: 1px solid rgba(213,186,11,0.2); padding-bottom: 0.5rem;">
                    <i class="fas fa-file-alt"></i> SEO
                </h5>
            </div>
            <div class="col-12">
                <label class="form-label">Meta Description (SEO)</label>
                <textarea name="meta_description" class="form-control" rows="2" maxlength="300"><?= esc($settings['meta_description'] ?? 'Business management platform built with NtoshiSoft Framework.') ?></textarea>
                <small class="text-muted">Appears in search engine results and social shares (max 300 chars).</small>
            </div>

            <!-- Notifications -->
            <div class="col-12 mt-3">
                <h5 style="color: var(--primary); border-bottom: 1px solid rgba(213,186,11,0.2); padding-bottom: 0.5rem;">
                    <i class="fas fa-bell"></i> Notifications
                </h5>
            </div>
            <div class="col-md-6">
                <label class="form-label">Admin Email (notifications)</label>
                <input type="email" name="admin_email" class="form-control" value="<?= esc($settings['admin_email'] ?? '') ?>">
            </div>
            <div class="col-md-6 d-flex align-items-end gap-4 pb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" value="1" <?= ($settings['email_notifications'] ?? '1') == '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="emailNotifications">Email notifications (admin)</label>
                </div>

            </div>

        </div>

        <div class="mt-4">
            <button type="submit" class="btn-primary" style="padding:0.7rem 2.5rem;">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<style>
.form-check-input:checked {
    background-color: var(--primary, #d5ba0b);
    border-color: var(--primary, #d5ba0b);
}
</style>

<?php $this->view('inc/footer'); ?>
