<?php
/** @var object $payment */
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

 
                            <!-- ROW 1: Payment Date & Client -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="payment_date">Payment Date</label>
                                    <div class="form-control"><?= $payment->payment_date ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="member">Member</label>
                                    <div class="form-control"><?= $payment->member ?></div>
                                </div>
                            </div>

                            <!-- ROW 2: Amount & Payment Method -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                    <label for="amount">Amount (R)</label>
                                    <div class="form-control"><?= $payment->amount ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="pay_type">Payments Type</label>
                                    <div class="form-control"><?= $payment->pay_type ?></div>
                                </div>
                            </div>

                            <!-- ROW 3: -->
                            <div class="row form-row mb-3">
                                <div class="col-lg-6">
                                  <label for="paid_via">Paid Via</label>
                                    <div class="form-control"><?= $payment->paid_via ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="notes">Notes</label>
                                    <div class="form-control"><?= $payment->notes ? $payment->notes : 'Not Applicable'  ?></div>
                                </div>
                            </div>


                            <!-- Submit Buttons -->
                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">DELETE PAYMENT</button>
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