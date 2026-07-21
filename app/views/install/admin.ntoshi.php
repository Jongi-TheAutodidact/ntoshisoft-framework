<?php
/** @var array $data */
/** @var ?string $error */
/** @var ?string $success */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card">
                <h2 class="gradient-text mb-4"><i class="fas fa-user-shield"></i> Admin Account</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">Admin account created successfully!</div>
                    <a href="<?= ROOT ?>/install/settings" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-arrow-right"></i> Next: Site Settings
                    </a>
                <?php else: ?>
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Surname</label>
                            <input type="text" name="surname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password (min 8 chars)</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-user-plus"></i> Create Admin
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
