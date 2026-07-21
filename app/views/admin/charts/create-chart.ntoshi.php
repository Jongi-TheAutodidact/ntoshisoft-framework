<?php
/** @var array $errors */
/** @var array $modules */
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
                        <h3 class="fs-4 page-title">Create New Chart</h3>
                        <hr>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="created_by" value="<?= user('firstname') . ' ' . user('surname') ?>">

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="chart_name">Chart Name</label>
                                    <input type="text" name="chart_name" value="<?= old_value('chart_name') ?>" class="form-control" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="chart_type">Chart Type</label>
                                    <select name="chart_type" class="form-control" required>
                                        <option value="bar" <?= old_value('chart_type') == 'bar' ? 'selected' : '' ?>>Bar Chart</option>
                                        <option value="line" <?= old_value('chart_type') == 'line' ? 'selected' : '' ?>>Line Chart</option>
                                        <option value="pie" <?= old_value('chart_type') == 'pie' ? 'selected' : '' ?>>Pie Chart</option>
                                        <option value="donut" <?= old_value('chart_type') == 'donut' ? 'selected' : '' ?>>Donut Chart</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="module">Data Module</label>
                                    <?php $selModule = old_value('module') ?>
                                    <select name="<?= esc('module') ?>" class="form-control ntoshi-search" id="module">
                                        <option value="">-- Select Module --</option>
                                        <?php if (!empty($modules)): foreach ($modules as $module): ?>
                                                <option value="<?= $module ?>" <?= $selModule == $module ? 'selected' : '' ?>><?= ucfirst($module) ?></option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                              
                                <div class="col-lg-6">
                                    <label for="data_source">Data Source</label>
                                    <select name="data_source" class="form-control" required>
                                        <option value="expense_by_type" <?= old_value('data_source') == 'expense_by_type' ? 'selected' : '' ?>>
                                            Expenses/Payments by Type
                                        </option>
                                        <option value="monthly_payments" <?= old_value('data_source') == 'monthly_payments' ? 'selected' : '' ?>>
                                            Monthly Payments
                                        </option>
                                        <option value="payment_methods" <?= old_value('data_source') == 'payment_methods' ? 'selected' : '' ?>>
                                            Payment Methods
                                        </option>
                                        <option value="client_status" <?= old_value('data_source') == 'client_status' ? 'selected' : '' ?>>
                                            Client Status
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="color_scheme">Color Scheme (comma separated hex codes)</label>
                                    <input type="text" name="color_scheme" value="<?= old_value('color_scheme', '#4e73df,#1cc88a,#36b9cc,#f6c23e,#e74a3b') ?>" class="form-control">
                                </div>
                                <div class="col-lg-3">
                                    <label for="width">Width (px)</label>
                                    <input type="number" name="width" value="<?= old_value('width', 600) ?>" class="form-control" min="300" max="1200">
                                </div>
                                <div class="col-lg-3">
                                    <label for="height">Height (px)</label>
                                    <input type="number" name="height" value="<?= old_value('height', 400) ?>" class="form-control" min="200" max="800">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">CREATE CHART</button>
                                    <a href="<?= ROOT ?>/admin/charts" class="btn btn-danger">CANCEL</a>
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