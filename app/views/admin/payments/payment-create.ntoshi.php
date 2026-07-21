<?php
/** @var array $errors */
/** @var array $clients */
/** @var array $data */
$this->view('inc/header', $data); ?>


<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--RECORD CREATED BY-->
                            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                           

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <?= Util::displayFlash('payment_update_success','success') ?>


                            <!-- ROW 1: Payment Date & Client -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <?= get_date_input('payment_date', old_value('payment_date'), 'Payment Date', ['class' => 'form-control']) ?>
                                </div>

                                <div class="col-lg-6">
                                   <label for="client">Client</label>
                                    <?php $selClient = old_value('client', basename($_GET['url'])) ?>
                                    <select name="<?= esc('client') ?>" class="form-control ntoshi-search" id="client">
                                    <option value="">-- Select Client --</option> 
                                    <hr>
                                    <option value="">N/A</option>
                                        <?php if (!empty($clients)): foreach ($clients as $client): ?>
                                            <option value="<?= $client->client_id ?>" <?= $selClient == $client->client_id ? 'selected' : '' ?>><?= esc($client->firstname) . ' ' . esc($client->surname) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- ROW 2: Amount & Payment Method -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="amount">Amount (R)</label>
                                    <input type="number" step="0.01" min="0" name="<?= esc('amount') ?>" value="<?= old_value('amount') ?>" class="form-control" id="amount" placeholder="e.g. 500.00">
                                </div>
                                <div class="col-lg-6">
                                    <label for="pay_type">Payment Type</label>
                                    <?php $selPaymentType = old_value('pay_type') ?>
                                    <select name="<?= esc('pay_type') ?>" class="form-control" id="pay_type">
                                        <option value="">-- Select Type --</option>
                                        <option value="Accessories" <?= $selPaymentType == "Accessories" ? "selected" : "" ?>>Accessories</option>
                                        <option value="Refund" <?= $selPaymentType == "Refund" ? "selected" : "" ?>>Refund</option>
                                        <option value="Debt" <?= $selPaymentType == "Debt" ? "selected" : "" ?>>Debt</option>
                                        <option value="Other" <?= $selPaymentType == "Other" ? "selected" : "" ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                             <!-- ROW 3: -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                   <?= get_payment_type_dropdown('paid_via', old_value('paid_via'), 'Paid Via')  ?>
                                </div>
                                <div class="col-lg-6">
                                    <label for="notes">Notes (Optional)</label>
                                    <textarea name="<?= esc('notes') ?>" class="form-control" id="notes" rows="3"><?= old_value('notes') ?></textarea>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">REGISTER PAYMENT</button>
                                    <a href="<?= ROOT ?>/admin/payments" class="btn btn-danger">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>