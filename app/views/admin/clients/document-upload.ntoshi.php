<?php
/** @var array $errors */
/** @var array $clients */
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
                        <form method="POST" action="" enctype="multipart/form-data">
                            <?= displayFormHeaderOnCreate() ?>

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Document Title *</label>
                                    <input type="text" name="title" value="<?= old_value('title') ?>"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="client_id">Client</label>
                                    <?php $selClient = old_value('client_id', basename($_GET['url'])) ?>
                                    <select name="<?= esc('client_id') ?>" class="form-control ntoshi-search" id="client_id">
                                        <option value="">-- Select Client --</option>
                                        <?php if($clients): foreach($clients as $client): $client_id = basename($_GET['url']); ?>
                                        <option value="<?= $client->client_id ?>" <?= $selClient == $client->client_id ? 'selected' : '' ?>><?= $client->firstname . ' ' . $client->surname  ?></option>
                                        <?php endforeach; endif ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= old_value('description') ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">File *</label>
                                    <input type="file" name="file" class="form-control" required>
                                    <div class="form-text">Allowed: PDF, DOC, XLS, JPG, PNG (Max 5MB)</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <?php foreach (['General', 'Contracts', 'Reports', 'Financials', 'Personnel'] as $cat): ?>
                                            <option value="<?= $cat ?>" <?= selected($cat, old_value('category')) ?>>
                                                <?= $cat ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-upload me-1"></i> Upload Document
                                </button>
                                <a href="<?= ROOT ?>/admin/document_uploads" class="btn btn-secondary">
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