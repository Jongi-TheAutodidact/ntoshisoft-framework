<?php
/** @var array $data */
/** @var ?string $error */
/** @var bool|null $db_created */
/** @var ?string $db_host */
/** @var ?string $db_port */
/** @var ?string $db_name */
/** @var ?string $db_user */
/** @var ?string $db_pass */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card">
                <h2 class="gradient-text mb-4"><i class="fas fa-database"></i> Database Configuration</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($db_created)): ?>
                    <div class="alert alert-success">Database connection successful!</div>
                <?php endif; ?>

                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Host</label>
                            <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($db_host ?? 'localhost') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Port</label>
                            <input type="text" name="db_port" class="form-control" value="<?= htmlspecialchars($db_port ?? '3306') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($db_name ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($db_user ?? 'root') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="db_pass" class="form-control" value="<?= htmlspecialchars($db_pass ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-plug"></i> Test &amp; Save
                    </button>
                </form>

                <?php if (!empty($db_created)): ?>
                    <a href="<?= ROOT ?>/install/run_migrations" class="btn-primary mt-3" style="display:inline-block;">
                        <i class="fas fa-arrow-right"></i> Create Tables
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
