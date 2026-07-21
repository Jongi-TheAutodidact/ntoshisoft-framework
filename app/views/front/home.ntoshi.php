<?php
/** @var string $siteName */
/** @var array $data */
$this->view('inc/header', $data); ?>

<div class="container">
    <div class="hero-section" style="text-align: center; margin-bottom: 3rem;">
        <h1 class="gradient-text" id="jb-hero-h1">
            <?= htmlspecialchars(APP_NAME ?? 'NtoshiSoft') ?>
        </h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Welcome to the platform</p>
    </div>

    <div class="glass-card" style="max-width: 800px; margin: 0 auto; text-align: center;">
        <div style="padding: 3rem 2rem;">
            <i class="fas fa-rocket" style="font-size: 4rem; color: #d5ba0b; margin-bottom: 1.5rem;"></i>
            <h2 class="gradient-text mb-3"><?= htmlspecialchars($siteName) ?></h2>
            <p style="margin-bottom: 2rem; line-height: 1.8;">
                A powerful, modular business management platform built on the NtoshiSoft Framework.
                Manage users, employees, clients, payments, expenditures, meetings and more — all from one place.
            </p>
            <?php if(!user()): ?>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?= ROOT ?>/auth/login" class="btn-primary" style="padding: 12px 32px;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
                <a href="<?= ROOT ?>/auth/register" class="btn-outline" style="padding: 12px 32px; text-decoration: none;">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<?php $this->view('inc/footer'); ?>
