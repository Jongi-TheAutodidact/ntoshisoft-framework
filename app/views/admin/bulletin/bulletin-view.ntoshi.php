<?php
/**
 * Single Bulletin View - Neon Enhanced
 * Displays detailed bulletin/announcement with glass morphism and neon effects
 * 
 * @var string $page_title
 * @var object $row - Bulletin object from database
 * @var array $data
 */
$this->view('inc/header', $data);
?>


        <div class="my-4">
            <a href="<?= ROOT ?>/admin/bulletin" class="ns-btn ns-btn-ghost ns-btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Bulletin Board
            </a>
        </div>

        <?php if (!empty($row)): ?>
            <!-- Hero Section with Title & Priority Badge -->
            <div class="ns-page-header mb-5" style="position: relative; overflow: hidden;">
                <div class="position-relative z-1">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <div class="ns-hero-badge mb-3">
                                <i class="bi bi-megaphone-fill me-2"></i>
                                Bulletin Announcement
                            </div>
                            <h1 class="display-heading fs-1 mb-3"><?= htmlspecialchars($row->title) ?></h1>
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                <!-- Priority Badge -->
                                <span class="ns-badge <?= 
                                    $row->priority == 'urgent' ? 'ns-badge-danger' : 
                                    ($row->priority == 'high' ? 'ns-badge-warning' : 'ns-badge-info')
                                ?> px-3 py-2 fs-6">
                                    <i class="bi bi-<?= $row->priority == 'urgent' ? 'exclamation-triangle-fill' : ($row->priority == 'high' ? 'arrow-up-circle-fill' : 'info-circle-fill') ?> me-1"></i>
                                    <?= ucfirst($row->priority) ?> Priority
                                </span>
                                
                                <!-- Category Badge -->
                                <span class="ns-badge ns-badge-success px-3 py-2 fs-6">
                                    <i class="bi bi-folder-fill me-1"></i>
                                    <?= ucfirst(str_replace('_', ' ', $row->category)) ?>
                                </span>
                                
                                <!-- Target Audience Badge -->
                                <span class="ns-badge ns-badge-primary px-3 py-2 fs-6">
                                    <i class="bi bi-people-fill me-1"></i>
                                    <?= ucfirst($row->target_audience) ?>
                                </span>
                                
                                <!-- Pinned Badge -->
                                <?php if ($row->is_pinned): ?>
                                <span class="ns-badge ns-badge-warning px-3 py-2 fs-6">
                                    <i class="bi bi-pin-angle-fill me-1"></i>
                                    Pinned
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ns-actions">
                            <a href="<?= ROOT ?>/admin/bulletin/edit/<?= $row->id ?>" class="ns-btn ns-btn-primary">
                                <i class="bi bi-pencil-fill"></i> Edit Bulletin
                            </a>
                            <a href="<?= ROOT ?>/admin/bulletin/delete/<?= $row->id ?>" class="ns-btn ns-btn-danger" onclick="return confirm('Are you sure you want to archive this bulletin?')">
                                <i class="bi bi-archive-fill"></i> Archive
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Animated background gradient -->
                <div class="position-absolute top-0 end-0 w-50 h-100 opacity-25" style="background: radial-gradient(circle at 100% 0%, var(--ns-neon-cyan), transparent); pointer-events: none;"></div>
            </div>

            <!-- Main Content Grid -->
            <div class="row g-4">
                <!-- Left Column: Main Content -->
                <div class="col-lg-8">
                    <!-- Content Card -->
                    <div class="ns-card mb-4">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-text-paragraph me-2"></i> Announcement Content
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <div class="lead" style="white-space: pre-line; font-size: 1.1rem; line-height: 1.8;">
                                <?= nl2br(htmlspecialchars($row->content)) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Attachment Card (if exists) -->
                    <?php if (!empty($row->attachment)): ?>
                    <div class="ns-card">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-paperclip me-2"></i> Attachment
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <a href="<?= ROOT . $row->attachment ?>" class="ns-btn ns-btn-secondary" target="_blank">
                                <i class="bi bi-file-earmark-arrow-down-fill"></i> Download Attachment
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Side Panel with Meta Info -->
                <div class="col-lg-4">
                    <!-- Publication Info Card -->
                    <div class="ns-card mb-4">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-calendar-event-fill me-2"></i> Publication Info
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="ns-stat-icon" style="width: 48px; height: 48px;">
                                    <i class="bi bi-calendar-plus fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase">Published Date</div>
                                    <div class="fw-bold"><?= date('l, F j, Y', strtotime($row->published_date)) ?></div>
                                    <div class="text-muted small"><?= date('g:i A', strtotime($row->published_date)) ?></div>
                                </div>
                            </div>

                            <?php if (!empty($row->expiry_date)): ?>
                            <div class="d-flex align-items-start gap-3 mb-4">
                                <div class="ns-stat-icon" style="width: 48px; height: 48px;">
                                    <i class="bi bi-calendar-x fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase">Expiry Date</div>
                                    <div class="fw-bold"><?= date('l, F j, Y', strtotime($row->expiry_date)) ?></div>
                                    <div class="text-muted small"><?= date('g:i A', strtotime($row->expiry_date)) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-start gap-3">
                                <div class="ns-stat-icon" style="width: 48px; height: 48px;">
                                    <i class="bi bi-person-badge fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small text-uppercase">Author</div>
                                    <div class="fw-bold"><?= htmlspecialchars($row->author_name) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Urgency Card -->
                    <div class="ns-card mb-4">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-graph-up me-2"></i> Status Overview
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <?php
                                $isExpired = !empty($row->expiry_date) && strtotime($row->expiry_date) < time();
                                $daysUntilExpiry = !empty($row->expiry_date) ? ceil((strtotime($row->expiry_date) - time()) / 86400) : null;
                            ?>
                            
                            <div class="text-center mb-3">
                                <div class="ns-stat-value mb-2">
                                    <?php if ($isExpired): ?>
                                        <span class="text-danger">Expired</span>
                                    <?php elseif ($row->priority == 'urgent'): ?>
                                        <span class="neon-text">Action Required</span>
                                    <?php else: ?>
                                        <span class="text-success">Active</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!$isExpired && !empty($row->expiry_date) && $daysUntilExpiry <= 7): ?>
                                    <div class="ns-badge ns-badge-warning">
                                        <i class="bi bi-clock-history"></i> Expires in <?= $daysUntilExpiry ?> days
                                    </div>
                                <?php elseif (!$isExpired && !empty($row->expiry_date)): ?>
                                    <div class="ns-badge ns-badge-info">
                                        <i class="bi bi-calendar-check"></i> <?= $daysUntilExpiry ?> days remaining
                                    </div>
                                <?php endif; ?>
                            </div>

                            <hr class="ns-border-glass my-3">
                            
                            <div class="d-flex justify-content-around">
                                <div class="text-center">
                                    <div class="text-muted small">Priority Level</div>
                                    <div class="fw-bold <?= $row->priority == 'urgent' ? 'text-danger' : ($row->priority == 'high' ? 'text-warning' : 'text-info') ?>">
                                        <?= str_repeat('⭐', $row->priority == 'urgent' ? 3 : ($row->priority == 'high' ? 2 : 1)) ?>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-muted small">Pinned Status</div>
                                    <div class="fw-bold"><?= $row->is_pinned ? '<i class="bi bi-pin-angle-fill text-success"></i> Pinned' : '<i class="bi bi-pin-angle text-muted"></i> Not Pinned' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="ns-card">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-lightning-charge-fill me-2"></i> Quick Actions
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= ROOT ?>/admin/bulletin/edit/<?= $row->id ?>" class="ns-btn ns-btn-primary">
                                    <i class="bi bi-pencil-fill"></i> Edit This Bulletin
                                </a>
                                <button class="ns-btn ns-btn-secondary" onclick="window.print();">
                                    <i class="bi bi-printer-fill"></i> Print Bulletin
                                </button>
                                <button class="ns-btn ns-btn-ghost" onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!')">
                                    <i class="bi bi-link-45deg"></i> Copy Share Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Share Card -->
                    <div class="ns-card mt-4">
                        <div class="ns-card-header">
                            <h5 class="neon-text mb-0">
                                <i class="bi bi-share-fill me-2"></i> Share Announcement
                            </h5>
                        </div>
                        <div class="ns-card-body">
                            <div class="d-flex justify-content-around">
                                <a href="#" class="ns-btn ns-btn-ghost ns-btn-sm" onclick="shareOnFacebook('<?= htmlspecialchars($row->title) ?>')">
                                    <i class="bi bi-facebook fs-5"></i>
                                </a>
                                <a href="#" class="ns-btn ns-btn-ghost ns-btn-sm" onclick="shareOnTwitter('<?= htmlspecialchars($row->title) ?>')">
                                    <i class="bi bi-twitter-x fs-5"></i>
                                </a>
                                <a href="#" class="ns-btn ns-btn-ghost ns-btn-sm" onclick="shareViaEmail('<?= htmlspecialchars($row->title) ?>')">
                                    <i class="bi bi-envelope-fill fs-5"></i>
                                </a>
                                <a href="#" class="ns-btn ns-btn-ghost ns-btn-sm" onclick="copyToClipboard()">
                                    <i class="bi bi-link-45deg fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expiry Warning Banner (if expiring soon) -->
            <?php if (!$isExpired && !empty($row->expiry_date) && $daysUntilExpiry <= 3): ?>
            <div class="ns-alert ns-alert-warning mt-4">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <strong>Expiring Soon!</strong> This bulletin will expire in <?= $daysUntilExpiry ?> days on <?= date('F j, Y', strtotime($row->expiry_date)) ?>.
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Error state -->
            <div class="ns-card text-center p-5">
                <div class="ns-stat-icon mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                </div>
                <h3 class="neon-text mb-3">Bulletin Not Found</h3>
                <p class="text-muted">The announcement you're looking for doesn't exist or has been archived.</p>
                <a href="<?= ROOT ?>/admin/bulletin" class="ns-btn ns-btn-primary">Back to Bulletin Board</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Share functions
function shareOnFacebook(title) {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter(title) {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this announcement: ${title}`);
    window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank', 'width=600,height=400');
}

function shareViaEmail(title) {
    const subject = encodeURIComponent(`Announcement: ${title}`);
    const body = encodeURIComponent(`Check out this announcement: ${window.location.href}`);
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
}
</script>

<?php $this->view('inc/footer', $data); ?>
   
