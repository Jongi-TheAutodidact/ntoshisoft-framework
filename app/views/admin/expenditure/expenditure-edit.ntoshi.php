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
                        <form method="POST" action="">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--RECORD UPDATED BY-->
                            <input type="hidden" name="<?= esc('updated_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD UPDATED-->
                            <input type="hidden" name="<?= esc('date_updated') ?>" value="<?= date('Y-m-d H:i:s') ?>">

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <?= get_date_input('expenditure_date', old_value('expenditure_date', $row->expenditure_date), 'Expenditure Date', ['class' => 'form-control']) ?>
                                </div>
                                <div class="col-lg-6">
                                    <label for="description">Description</label>
                                    <input type="text" name="<?= esc('description') ?>" value="<?= old_value('description', $row->description) ?>" class="form-control" id="description">
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="amount">Amount (R)</label>
                                    <input type="number" step="0.01" min="0" name="<?= esc('amount') ?>" value="<?= old_value('amount', $row->amount) ?>" class="form-control" id="amount">
                                </div>
                                <div class="col-lg-6">
                                    <label for="expense_type">Expense Type</label>
                                    <?php $selExpenseType = old_value('expense_type', $row->expense_type) ?>
                                    <select name="<?= esc('expense_type') ?>" class="form-control" id="expense_type">
                                        <option value="Office Supplies" <?= $selExpenseType == 'Office Supplies' ? 'selected' : '' ?>>Office Supplies</option>
                                        <option value="Salaries" <?= $selExpenseType == 'Salaries' ? 'selected' : '' ?>>Salaries</option>
                                        <option value="Utilities" <?= $selExpenseType == 'Utilities' ? 'selected' : '' ?>>Utilities</option>
                                        <option value="Maintenance" <?= $selExpenseType == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                        <option value="Marketing" <?= $selExpenseType == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                        <option value="Other" <?= $selExpenseType == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <?= get_payment_type_dropdown('paid_via', old_value('paid_via', $row->paid_via), 'Paid Via') ?>
                                </div>
                                <div class="col-lg-6">
                                    <label for="notes">Notes</label>
                                    <textarea name="<?= esc('notes') ?>" class="form-control" id="notes"><?= old_value('notes', $row->notes) ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">EDIT EXPENDITURE</button>
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