<?php
/** @var array $data */
/** @var ?string $error */
/** @var ?string $success */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card">
                <h2 class="gradient-text mb-4"><i class="fas fa-cog"></i> Site Settings</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">Settings saved successfully!</div>
                    <a href="<?= ROOT ?>/install/finish" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-arrow-right"></i> Next: Finalize
                    </a>
                <?php else: ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="NtoshiSoft  Form" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">App Domain</label>
                            <input type="text" name="app_domain" class="form-control" value="<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Admin Email</label>
                            <input type="email" name="admin_email" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
