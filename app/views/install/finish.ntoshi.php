<?php
/** @var array $data */
/** @var ?string $login_url */
$this->view('inc/header', $data); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card text-center">
                <div style="font-size:4rem;margin-bottom:1rem;">
                    <i class="fas fa-check-circle" style="color:#28a745;"></i>
                </div>
                <h2 class="gradient-text mb-3">Installation Complete!</h2>
                <p>Your NtoshiSoft  application is ready.</p>

                <hr style="border-color:rgba(0,255,255,0.1);">

                <div class="text-start" style="max-width:400px;margin:0 auto;">
                    <p><i class="fas fa-check-circle" style="color:#28a745;"></i> Database configured</p>
                    <p><i class="fas fa-check-circle" style="color:#28a745;"></i> Tables created</p>
                    <p><i class="fas fa-check-circle" style="color:#28a745;"></i> Admin account created</p>
                    <p><i class="fas fa-check-circle" style="color:#28a745;"></i> Site settings saved</p>
                    <p><i class="fas fa-check-circle" style="color:#28a745;"></i> .env file written</p>
                </div>

                <?php if (!empty($login_url)): ?>
                <a href="<?= $login_url ?>" class="btn-primary mt-3" style="display:inline-block;font-size:1.1rem;padding:0.8rem 3rem;">
                    <i class="fas fa-arrow-right"></i> Go to Login
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
