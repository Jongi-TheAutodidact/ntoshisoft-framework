<?php
/** @var array $documents */
/** @var array $categories */
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fs-4 page-title">Document Management</h3>
                            <a href="<?= ROOT . '/admin/document/upload' ?>" class="btn btn-warning text-dark">
                                <i class="bi bi-upload me-2"></i>Upload New
                            </a>
                        </div>
                        <hr>
                        
                        <?= Util::displayFlash('document_upload_success', 'success') ?>
                        <?= Util::displayFlash('document_update_success', 'success') ?>
                        <?= Util::displayFlash('document_delete_success', 'success') ?>
                        <?= Util::displayFlash('document_error', 'danger') ?>
                        
                        <div class="table-responsive">
                            <table class="table ntoshitable table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Category</th>
                                        <th>Upload Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($documents)): ?>
                                        <?php $counter = 1; ?>
                                        <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= esc($doc->title) ?></td>
                                                <td><?= strtoupper($doc->file_type) ?></td>
                                                <td><?= formatSizeUnits($doc->file_size) ?></td>
                                                <td><?= esc($doc->category) ?></td>
                                                <td><?= date('M j, Y', strtotime($doc->date_created)) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $doc->status == 'Active' ? 'success' : 'secondary' ?>">
                                                        <?= $doc->status ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= ROOT . '/' . $doc->file_path ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= ROOT ?>/admin/document_upload/edit/<?= $doc->id ?>" 
                                                       class="btn btn-sm btn-warning me-1">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="<?= ROOT ?>/admin/document_upload/delete/<?= $doc->id ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Delete this document?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No documents found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">Documents by Category</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($categories as $category): ?>
                                        <?php $docs = (new DocumentUpload())->getDocumentsByCategory($category); ?>
                                        <?php if (!empty($docs)): ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6><?= $category ?></h6>
                                                        <p class="mb-0"><?= count($docs) ?> document(s)</p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer') ?>