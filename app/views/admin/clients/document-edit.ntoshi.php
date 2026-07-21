<?php
/** @var array $errors */
/** @var object $document */
$this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="updated_by" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Title *</label>
                                    <input type="text" name="title" value="<?= old_value('title', $document->title) ?>" 
                                           class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <?php foreach (['General', 'Contracts', 'Reports', 'Financials', 'Personnel'] as $cat): ?>
                                            <option value="<?= $cat ?>" <?= selected($cat, old_value('category', $document->category)) ?>>
                                                <?= $cat ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= old_value('description', $document->description) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Current File</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?= basename($document->file_path) ?>" readonly>
                                    <a href="<?= ROOT . '/' . $document->file_path ?>" 
                                       target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Active" <?= selected('Active', old_value('status', $document->status)) ?>>Active</option>
                                        <option value="Archived" <?= selected('Archived', old_value('status', $document->status)) ?>>Archived</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-save me-1"></i> Update Document
                                </button>
                                <a href="<?= ROOT ?>/admin/documents" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer') ?>