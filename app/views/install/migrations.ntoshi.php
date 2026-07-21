<?php
/** @var array $data */
/** @var ?string $error */
/** @var ?string $success */
/** @var array $results */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card">
                <h2 class="gradient-text mb-4"><i class="fas fa-table"></i> Database Tables</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">All tables created successfully!</div>

                    <div class="mt-3">
                        <h5>Created Tables:</h5>
                        <ul>
                            <?php foreach ($results as $r): ?>
                                <li><code><?= $r['table'] ?></code> — <?= $r['success'] ? '<span style="color:#28a745;">OK</span>' : '<span style="color:#dc3545;">Failed</span>' ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <p class="text-muted mt-3">Default settings have been populated. Now let's create your admin account.</p>
                    <a href="<?= ROOT ?>/install/admin" class="btn-primary" style="display:inline-block;">
                        <i class="fas fa-arrow-right"></i> Create Admin Account
                    </a>
                <?php else: ?>
                    <p>Click the button below to create all required tables and populate default settings.</p>
                    <form method="post">
                        <input type="hidden" name="run" value="1">
                        <button type="submit" class="btn-primary" style="display:inline-block;">
                            <i class="fas fa-play"></i> Create Tables
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
