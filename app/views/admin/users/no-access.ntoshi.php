<?php
/**
 * @var string $page_title
 * @var array $data
 * @var array $errors
 */
$this->view('inc/header', $data)
?>

<div class="ns-no-access-wrapper">
    <div class="ns-no-access-card glass-card">
        <!-- Exclamation Image -->
        <div class="ns-no-access-icon">
            <img src="<?= ROOT . '/assets/img/exclamation-sign.jpg' ?>" alt="Access Denied" class="ns-no-access-image">
        </div>

        <!-- Main Message -->
        <h1 class="ns-no-access-title gradient-text">Access Denied</h1>
        
        <div class="ns-no-access-message">
            <p class="ns-no-access-main-text">
                <strong>Oops!</strong> You don't have permission to access this page.
            </p>
            <p class="ns-no-access-sub-text text-muted">
                This area is restricted to administrators only. 
                Please contact your system administrator if you believe this is an error.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="ns-no-access-actions">
            <a href="<?= ROOT ?>" class="btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                Return to Home
            </a>
            
            <a href="javascript:history.back()" class="btn-outline">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Go Back
            </a>
        </div>

        <!-- Error Reference -->
        <div class="ns-no-access-reference">
            <span class="text-muted">Error Code: 403 - Forbidden</span>
        </div>
    </div>
</div>

<style>
/* ============================================
   NO ACCESS PAGE - Minimal Framework Overrides
   ============================================ */

.ns-no-access-wrapper {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--ns-space-lg) 1.5rem;
    position: relative;
    z-index: 1;
}

.ns-no-access-card {
    max-width: 580px;
    width: 100%;
    padding: 3rem 2.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: nsSlideUp 0.5s var(--ns-transition-medium);
}

/* Decorative accent line using framework colors */
.ns-no-access-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, 
        var(--ns-accent) 0%, 
        rgba(var(--ns-accent-rgb), 0.5) 50%, 
        var(--ns-accent) 100%
    );
    background-size: 200% 100%;
    animation: nsGradientMove 3s ease infinite;
}

/* Icon/Image Styles */
.ns-no-access-icon {
    margin-bottom: 1.5rem;
}

.ns-no-access-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid var(--ns-glass-border);
    padding: 8px;
    background: var(--ns-bg-primary);
    transition: all var(--ns-transition-medium);
}

.ns-no-access-image:hover {
    transform: scale(1.05) rotate(-3deg);
    border-color: var(--ns-accent);
    box-shadow: 0 0 30px var(--ns-accent-glow);
}

/* Typography */
.ns-no-access-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 1rem 0;
    letter-spacing: -0.5px;
}

.ns-no-access-message {
    margin: 1.5rem 0 2rem 0;
}

.ns-no-access-main-text {
    font-size: 1.1rem;
    color: var(--ns-text-primary);
    margin-bottom: 0.75rem;
    line-height: 1.6;
}

.ns-no-access-main-text strong {
    color: #e74c3c;
    font-weight: 700;
}

.ns-no-access-sub-text {
    font-size: 0.95rem;
    line-height: 1.8;
    margin: 0;
}

/* Action Buttons */
.ns-no-access-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin: 2rem 0 1.5rem 0;
}

.ns-no-access-actions .btn-primary,
.ns-no-access-actions .btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.7rem 1.8rem;
    font-size: 0.95rem;
    min-width: 160px;
    justify-content: center;
}

.ns-no-access-actions .btn-primary svg,
.ns-no-access-actions .btn-outline svg {
    transition: transform var(--ns-transition-fast);
}

.ns-no-access-actions .btn-primary:hover svg {
    transform: translateX(-3px);
}

.ns-no-access-actions .btn-outline:hover svg {
    transform: translateX(-3px);
}

/* Error Reference */
.ns-no-access-reference {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--ns-glass-border);
}

.ns-no-access-reference span {
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Animations */
@keyframes nsSlideUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes nsGradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */

@media (max-width: 768px) {
    .ns-no-access-wrapper {
        min-height: 60vh;
        padding: 1rem;
    }

    .ns-no-access-card {
        padding: 2.5rem 1.5rem;
    }

    .ns-no-access-image {
        width: 100px;
        height: 100px;
    }

    .ns-no-access-title {
        font-size: 2rem;
    }

    .ns-no-access-main-text {
        font-size: 1rem;
    }

    .ns-no-access-sub-text {
        font-size: 0.9rem;
    }

    .ns-no-access-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 0.8rem;
    }

    .ns-no-access-actions .btn-primary,
    .ns-no-access-actions .btn-outline {
        min-width: auto;
        width: 100%;
        padding: 0.8rem 1.5rem;
    }
}

@media (max-width: 480px) {
    .ns-no-access-card {
        padding: 2rem 1rem;
    }

    .ns-no-access-image {
        width: 80px;
        height: 80px;
        padding: 6px;
    }

    .ns-no-access-title {
        font-size: 1.6rem;
    }

    .ns-no-access-main-text {
        font-size: 0.95rem;
    }

    .ns-no-access-sub-text {
        font-size: 0.85rem;
    }

    .ns-no-access-actions .btn-primary,
    .ns-no-access-actions .btn-outline {
        font-size: 0.85rem;
        padding: 0.7rem 1.2rem;
    }
}

/* Light mode overrides - automatically handled by framework */
/* The framework's body.light styles will apply to .glass-card, .gradient-text, etc. */
body.light .ns-no-access-main-text strong {
    color: #dc3545;
}
</style>

<?php $this->view('inc/footer'); ?>