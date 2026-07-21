<?php
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
                            
                            <div class="alert alert-danger text-center">
                                <h5><i class="bi bi-exclamation-triangle me-2"></i>Confirm Document Deletion</h5>
                                <p class="mb-0">This action cannot be undone</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <div class="form-control"><?= $document->title ?></div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">File Type</label>
                                    <div class="form-control"><?= strtoupper($document->file_type) ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File Size</label>
                                    <div class="form-control"><?= formatSizeUnits($document->file_size) ?></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Upload Date</label>
                                <div class="form-control"><?= date('F j, Y', strtotime($document->date_created)) ?></div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i> Confirm Delete
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