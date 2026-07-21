<?php
/** @var array $data */
/** @var bool $php_ok */
/** @var array $extensions */
/** @var array $writable_dirs */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card">
                <h2 class="gradient-text mb-4"><i class="fas fa-rocket"></i> System Installation</h2>
                <p class="text-muted">Welcome to the installation wizard. Let's check your server meets the requirements.</p>

                <div class="mt-4">
                    <h5>PHP Version</h5>
                    <div class="d-flex justify-content-between align-items-center p-2" style="background:rgba(0,0,0,0.2);border-radius:0.5rem;">
                        <span><?= phpversion() ?></span>
                        <?php if ($php_ok): ?>
                            <span class="badge" style="background:rgba(40,167,69,0.2);color:#28a745;"><i class="fas fa-check"></i> OK</span>
                        <?php else: ?>
                            <span class="badge" style="background:rgba(220,53,69,0.2);color:#dc3545;"><i class="fas fa-times"></i> Requires 7.4+</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <h5>Required Extensions</h5>
                    <?php foreach ($extensions as $ext => $loaded): ?>
                        <div class="d-flex justify-content-between align-items-center p-2 mt-1" style="background:rgba(0,0,0,0.2);border-radius:0.5rem;">
                            <span><?= strtoupper($ext) ?></span>
                            <?php if ($loaded): ?>
                                <span class="badge" style="background:rgba(40,167,69,0.2);color:#28a745;"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(220,53,69,0.2);color:#dc3545;"><i class="fas fa-times"></i></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-3">
                    <h5>Directory Permissions</h5>
                    <?php foreach ($writable_dirs as $dir => $writable): ?>
                        <div class="d-flex justify-content-between align-items-center p-2 mt-1" style="background:rgba(0,0,0,0.2);border-radius:0.5rem;">
                            <span><code><?= $dir ?></code></span>
                            <?php if ($writable): ?>
                                <span class="badge" style="background:rgba(40,167,69,0.2);color:#28a745;">Writable</span>
                            <?php else: ?>
                                <span class="badge" style="background:rgba(220,53,69,0.2);color:#dc3545;">Not Writable</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <a href="<?= ROOT ?>/install/database" class="btn-primary mt-4" style="display:inline-block;">
                    <i class="fas fa-arrow-right"></i> Start Installation
                </a>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
