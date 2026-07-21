<?php
/** @var array $charts */
$this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title">Chart Gallery</h3>
                <a href="<?= ROOT . '/admin/chart/create' ?>" class="btn btn-warning text-dark">
                    <i class="bi bi-plus-circle me-2"></i>Create New Chart
                </a>
            </div>
            <hr>

            <?= Util::displayFlash('chart_create_success', 'success') ?>
            <?= Util::displayFlash('chart_edit_success', 'success') ?>
            <?= Util::displayFlash('chart_delete_success', 'success') ?>

            <div class="row">
                <?php if (!empty($charts)): ?>
                    <?php foreach ($charts as $chart): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><?= esc($chart->chart_name) ?></h5>
                                    <div>
                                        <span class="badge bg-info"><?= ucfirst($chart->chart_type) ?></span>
                                        <span class="badge bg-secondary"><?= $chart->module ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container text-center">
                                        <img src="<?= ROOT ?>/admin/chart/render/<?= $chart->id ?>?t=<?= time() ?>"
                                            alt="<?= esc($chart->chart_name) ?>"
                                            class="img-fluid border rounded p-2 bg-white"
                                            style="max-height: 300px;">
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <small class="text-muted">
                                        Created by <?= esc($chart->created_by) ?>
                                        on <?= date('M j, Y', strtotime($chart->date_created)) ?>
                                    </small>
                                    <div>
                                        <a href="<?= ROOT ?>/admin/chart/edit/<?= $chart->id ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="<?= ROOT ?>/admin/chart/delete/<?= $chart->id ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this chart?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            No charts created yet. Click "Create New Chart" to get started.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>


<?php $this->view('inc/footer') ?>