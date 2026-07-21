<?php 
    /**
     * @var string $page_title
     * @var array $data
     * @var object $drivers
     */
    $this->view('inc/header', $data); 
?>
            <div class="d-flex justify-content-between align-items-center my-4">
                <h2><?= htmlspecialchars($page_title) ?></h2>
                <a href="<?= ROOT ?>/admin/bulletin" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Bulletin Board
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="6"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="announcement">Announcement</option>
                                    <option value="reminder">Reminder</option>
                                    <option value="news">News</option>
                                    <option value="alert">Alert</option>
                                    <option value="event">Event</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="target_audience" class="form-label">Target Audience *</label>
                                <select class="form-select" id="target_audience" name="target_audience">
                                    <option value="all">All</option>
                                    <option value="parents">Parents</option>
                                    <option value="staff">Staff</option>
                                    <option value="teachers">Teachers</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="published_date" class="form-label">Published Date *</label>
                                <input type="date" class="form-control" id="published_date" name="published_date" value="<?= htmlspecialchars($_POST['published_date'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($_POST['expiry_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="is_pinned" class="form-label">Pin to Top</label>
                                <select class="form-select" id="is_pinned" name="is_pinned">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                        </div>

                        <button type="submit" class="btn btn-primary">Create Post</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../app/views/inc/footer.ntoshi.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
