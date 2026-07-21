<?php
    /**
     * @var array $expenditures
     * @var array $payment
     * @var float $net_balance
     * @var float $sum_all_expenditures
     * @var array $expense_types
     * @var array $data
     */
    $this->view('inc/header', $data); ?>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fs-4 page-title">Expenditure Records</h3>
                            <a href="<?= ROOT . '/admin/expenditure/create' ?>" class="btn btn-warning text-dark">
                                <i class="bi bi-plus-circle me-2"></i>Add New Expenditure
                            </a>
                        </div>

                        <hr>
                        
                        <?= Util::displayFlash('expenditure_register_success', 'success') ?>
                        <?= Util::displayFlash('expenditure_update_success', 'success') ?>
                        <?= Util::displayFlash('expenditure_delete_success', 'success') ?>
                        
                        <!-- Financial Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Payments</h5>
                                        <p class="card-text fs-4">R <?= $sum_all_payments = $payment['sum_all_payments'] ?? 0; number_format($sum_all_payments, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Expenditure</h5>
                                        <p class="card-text fs-4">R <?= $sum_all_expenditures = $payment['sum_all_expenditures'] ?? 0; number_format($sum_all_expenditures, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-<?= ($net_balance >= 0) ? 'primary' : 'warning' ?> text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Net Balance</h5>
                                        <p class="card-text fs-4">R<?= number_format($net_balance, 2) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenditure Table -->
                        <div class="table-responsive">
                            <table class="table ntoshitable table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($expenditures)): ?>
                                        <?php $counter = 1; ?>
                                        <?php foreach ($expenditures as $exp): ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= date('F j, Y', strtotime($exp->expenditure_date)) ?></td>
                                                <td><?= esc($exp->description) ?></td>
                                                <td>R <?= number_format($exp->amount, 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= 
                                                    ($exp->expense_type == 'Office Supplies') ? 'primary' : 
                                                    (($exp->expense_type == 'Salaries') ? 'info' : 
                                                    (($exp->expense_type == 'Utilities') ? 'success' : 
                                                    (($exp->expense_type == 'Maintenance') ? 'warning' : 
                                                    (($exp->expense_type == 'Marketing') ? 'danger' : 'secondary')))) ?>">
                                                        <?= $exp->expense_type ?>
                                                    </span>
                                                </td>
                                                <td><?= $exp->paid_via ?></td>
                                                <td>
                                                    <a href="<?= ROOT ?>/admin/expenditure/edit/<?= $exp->id ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="<?= ROOT ?>/admin/expenditure/delete/<?= $exp->id ?>" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this expenditure record?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No expenditure records found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">TOTAL EXPENDITURE:</td>
                                        <td>R<?= number_format($sum_all_expenditures, 2) ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Expense Type Breakdown -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">Expense Type Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($expense_types as $type => $total): ?>
                                        <?php if ($total > 0): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span><?= $type ?></span>
                                                    <span>R<?= number_format($total, 2) ?></span>
                                                </div>
                                                <div class="progress mt-1" style="height: 10px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= ($total/$sum_all_expenditures)*100 ?>%"></div>
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