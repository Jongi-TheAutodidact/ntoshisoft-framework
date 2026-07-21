<?php 
    /**
     * @var string $page_title
     * @var array $data
     * @var object $drivers
     * @var array $rows
     * @var object $row
     */
    $this->view('inc/header', $data); 
?>
            <div class="d-flex justify-content-between align-items-center my-4">
                <h2><?= htmlspecialchars($page_title) ?></h2>
                <a href="<?= ROOT ?>/admin/bulletin/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Post
                </a>
            </div>

            <?php if (!empty($_SESSION['bulletin_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['bulletin_success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['bulletin_success']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Audience</th>
                                    <th>Priority</th>
                                    <th>Published</th>
                                    <th>Pinned</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): ?>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row->title) ?></td>
                                            <td><span class="badge bg-secondary"><?= ucfirst($row->category) ?></span></td>
                                            <td><?= ucfirst($row->target_audience) ?></td>
                                            <td><span class="badge bg-<?= $row->priority == 'urgent' ? 'danger' : ($row->priority == 'high' ? 'warning' : 'info') ?>"><?= ucfirst($row->priority) ?></span></td>
                                            <td><?= htmlspecialchars($row->published_date) ?></td>
                                            <td><?= $row->is_pinned ? '<i class="bi bi-pin-angle text-danger"></i>' : '-' ?></td>
                                            <td>
                                                <a href="<?= ROOT ?>/admin/bulletin/view/<?= $row->id ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= ROOT ?>/admin/bulletin/edit/<?= $row->id ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= ROOT ?>/admin/bulletin/delete/<?= $row->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-archive"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No posts yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../app/views/inc/footer.ntoshi.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
