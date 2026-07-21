<?php
/** @var array $pinned */
/** @var array $posts */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Board - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../app/views/inc/front-header.ntoshi.php'; ?>

    <main class="container mt-4">
        <h2>Bulletin Board</h2>

        <?php if (!empty($pinned)): ?>
            <div class="mb-4">
                <h4><i class="bi bi-pin-angle text-danger"></i> Pinned Posts</h4>
                <?php foreach ($pinned as $post): ?>
                    <div class="card border-danger mb-3">
                        <div class="card-header bg-danger text-white">
                            <?= htmlspecialchars($post->title) ?>
                            <span class="badge bg-light text-dark"><?= ucfirst($post->category) ?></span>
                        </div>
                        <div class="card-body">
                            <?= nl2br(htmlspecialchars($post->content)) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0"><?= htmlspecialchars($post->title) ?></h5>
                                <small class="text-muted">
                                    <?= htmlspecialchars($post->published_date) ?> | 
                                    <span class="badge bg-<?= $post->priority == 'urgent' ? 'danger' : ($post->priority == 'high' ? 'warning' : 'info') ?>">
                                        <?= ucfirst($post->priority) ?>
                                    </span>
                                </small>
                            </div>
                            <div class="card-body">
                                <?= nl2br(substr(htmlspecialchars($post->content), 0, 200)) ?>...
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">By: <?= htmlspecialchars($post->author_name) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No posts available at this time.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php require_once '../app/views/inc/front-footer.ntoshi.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
