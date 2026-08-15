<?php
    /**
     * @var object $row
     * @var array $data
     */
    $this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <form method="POST" id="employee-delete" data-offline-table="employees" data-offline-action="delete" data-offline-id="<?= $row->id ?>">
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">

                            <div class="text-danger mb-4">
                                <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                <h3 class="mt-3">Confirm Deletion</h3>
                            </div>

                            <div class="alert alert-danger">
                                <p>You are about to permanently delete:</p>
                                <h4 class="text-dark"><?= esc($row->firstname . ' ' . $row->surname) ?></h4>
                                <p class="mb-1"><i class="bi bi-person-badge me-2"></i> <?= esc($row->position) ?></p>
                                <p class="mb-1"><i class="bi bi-upc-scan me-2"></i> <?= esc($row->employee_number) ?></p>
                                <p>This action <strong>cannot be undone</strong>.</p>
                            </div>

                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you ABSOLUTELY sure? This will permanently delete this employee record!')">
                                    <i class="bi bi-trash3-fill me-1"></i> Confirm Delete
                                </button>
                                <a href="<?= ROOT ?>/admin/employees/view/<?= $row->id ?>" class="btn btn-warning text-dark">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Cancel
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