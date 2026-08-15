<?php
    /**
     * @var array $errors
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" data-offline-table="expenditures" data-offline-action="delete" data-offline-id="<?= $row->id ?>">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--RECORD DELETED BY-->
                            <input type="hidden" name="<?= esc('deleted_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD DELETED-->
                            <input type="hidden" name="<?= esc('date_deleted') ?>" value="<?= date('Y-m-d H:i:s') ?>">

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="expenditure_date">Date</label>
                                    <div class="form-control"><?= $row->expenditure_date ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="description">Description</label>
                                   <div class="form-control"><?= $row->description ?></div>
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="amount">Amount (R)</label>
                                    <div class="form-control"><?= $row->amount ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="expense_type">Expense Type</label>
                                    <div class="form-control"><?= $row->expense_type ?></div>
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="paid_via">Paid Via</label>
                                    <div class="form-control"><?= $row->paid_via ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="notes">Notes</label>
                                    <div class="form-control"><?= $row->notes ?></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">DELETE EXPENDITURE</button>
                                    <a href="<?= ROOT ?>/admin/expenditure" class="btn btn-danger">CANCEL</a>
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