<?php
/** @var object $row */
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
                        <form method="POST" id="order-create" enctype="multipart/form-data" data-offline-table="clients" data-offline-action="delete" data-offline-id="<?= $row->id ?>">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">

                            <!--ROW 1-->
                            <div class="row form-row">
                                <div class="col-lg-4">
                                    <label for="user_id">User</label>
                                    <div class="form-control"><?= $row->user_id ?></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="policy_holder">Policy Holder</label>
                                    <div class="form-control"><?= $row->policy_holder ?></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="identity_number">ID Number (Govt Issued)</label>
                                    <div class="form-control"><?= $row->identity_number ?></div>
                                </div>
                            </div>
                            <!--ROW 2-->
                            <div class="row form-row">
                                <div class="col-lg-4">
                                    <label for="address">Street Address</label>
                                    <div class="form-control"><?= $row->address ?></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="city">City</label>
                                    <div class="form-control"><?= $row->city ?></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="postal_code">Postal Code</label>
                                    <div class="form-control"><?= $row->postal_code ?></div>
                                </div>
                            </div>
                            <!--ROW 3-->
                            <div class="row form-row">
                                <div class="col-lg-4">
                                    <label for="province">Province</label>
                                    <div class="form-control"><?= $row->province ?></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="country">Country</label>
                                    <div class="form-control"><?= $row->country ?></div>
                                </div>
                                <div class="col-lg-12">
                                    <label for="status">Status</label>
                                    <div class="form-control"><?= $row->status ?></div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">DELETE CLIENT</button>
                                    <a href="<?= ROOT ?>/admin/clients" class="btn btn-danger">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>