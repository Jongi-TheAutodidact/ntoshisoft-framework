<?php 
    /**
     * @var string $page_title
     * @var array $data
     * @var object $drivers
     * @var array $errors
     * @var object $row
     */
    $this->view('inc/header', $data); 
?>
            <div class="d-flex justify-content-between align-items-center my-4">
                <h2><?= htmlspecialchars($page_title) ?></h2>
                <a href="<?= ROOT ?>/admin/bulletin" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Bulletin Board
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $row->title ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="6"><?= htmlspecialchars($_POST['content'] ?? $row->content ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="announcement" <?= ($row->category ?? '' == 'announcement') ? 'selected' : '' ?>>Announcement</option>
                                    <option value="reminder" <?= ($row->category ?? '' == 'reminder') ? 'selected' : '' ?>>Reminder</option>
                                    <option value="news" <?= ($row->category ?? '' == 'news') ? 'selected' : '' ?>>News</option>
                                    <option value="alert" <?= ($row->category ?? '' == 'alert') ? 'selected' : '' ?>>Alert</option>
                                    <option value="event" <?= ($row->category ?? '' == 'event') ? 'selected' : '' ?>>Event</option>
                                    <option value="general" <?= ($row->category ?? '' == 'general') ? 'selected' : '' ?>>General</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="target_audience" class="form-label">Target Audience *</label>
                                <select class="form-select" id="target_audience" name="target_audience">
                                    <option value="all" <?= ($row->target_audience ?? '' == 'all') ? 'selected' : '' ?>>All</option>
                                    <option value="parents" <?= ($row->target_audience ?? '' == 'parents') ? 'selected' : '' ?>>Parents</option>
                                    <option value="staff" <?= ($row->target_audience ?? '' == 'staff') ? 'selected' : '' ?>>Staff</option>
                                    <option value="teachers" <?= ($row->target_audience ?? '' == 'teachers') ? 'selected' : '' ?>>Teachers</option>
                                    <option value="admin" <?= ($row->target_audience ?? '' == 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low" <?= ($row->priority ?? '' == 'low') ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= ($row->priority ?? 'medium' == 'medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= ($row->priority ?? '' == 'high') ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= ($row->priority ?? '' == 'urgent') ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="published_date" class="form-label">Published Date *</label>
                                <input type="date" class="form-control" id="published_date" name="published_date" value="<?= htmlspecialchars($_POST['published_date'] ?? $row->published_date ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($_POST['expiry_date'] ?? $row->expiry_date ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="is_pinned" class="form-label">Pin to Top</label>
                                <select class="form-select" id="is_pinned" name="is_pinned">
                                    <option value="0" <?= ($row->is_pinned ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= ($row->is_pinned ?? 0) == 1 ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                            <?php if (!empty($row->attachment) && file_exists($row->attachment)): ?>
                                <a href="<?= ROOT . $row->attachment ?>" target="_blank">View Current Attachment</a>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Post</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../app/views/inc/footer.ntoshi.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
