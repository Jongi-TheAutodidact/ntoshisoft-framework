<?php
/** @var object $chart */
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
                        <h3 class="fs-4 page-title">Delete Chart: <?= esc($chart->chart_name) ?></h3>
                        <hr>

                        <div class="alert alert-warning">
                            <h5><i class="bi bi-exclamation-triangle-fill"></i> Warning!</h5>
                            <p>You are about to permanently delete this chart configuration. This action cannot be undone.</p>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Chart Preview</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="<?= ROOT ?>/admin/chart/render/<?= $chart->id ?>" 
                                     alt="<?= esc($chart->chart_name) ?>" 
                                     class="img-fluid" 
                                     style="max-height: 300px;">
                            </div>
                        </div>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <div class="form-group mb-3">
                                <label for="confirm">Type "DELETE" to confirm:</label>
                                <input type="text" name="confirm" class="form-control" required>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-danger">CONFIRM DELETION</button>
                                    <a href="<?= ROOT ?>/admin/charts" class="btn btn-outline-secondary">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer') ?>