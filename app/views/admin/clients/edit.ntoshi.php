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
                        <form method="POST" action="" id="user-new" enctype="multipart/form-data">
                            <?= displayFormHeaderOnUpdate() ?>

                            <!--ROW 1-->
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <label for="user_id">User</label>
                                    <div class="form-control"><?= $row->user_id ?></div>
                                </div>
                                
                                <div class="col-lg-6">
                                    <label for="identity_number">ID Number (Govt Issued)</label>
                                    <input type="text" name="<?= esc('identity_number') ?>" value="<?= old_value('identity_number', $row->identity_number) ?>" class="form-control mb-1" id="identity_number" placeholder="e.g. 6303215854087">
                                </div>
                            </div>
                            <!--ROW 2-->
                            <div class="row form-row">
                                <div class="col-lg-4">
                                    <label for="address">Street Address</label>
                                    <input type="text" name="<?= esc('address') ?>" value="<?= old_value('address', $row->address) ?>" class="form-control mb-1" id="address" placeholder="e.g. 12 Evelyn Street">
                                </div>
                                <div class="col-lg-4">
                                    <label for="city">City</label>
                                    <input type="text" name="<?= esc('city') ?>" value="<?= old_value('city', $row->city) ?>" class="form-control mb-1" id="city">
                                </div>
                                <div class="col-lg-4">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="number" name="<?= esc('postal_code') ?>" value="<?= old_value('postal_code', $row->postal_code) ?>" class="form-control mb-1" id="postal_code">
                                </div>
                            </div>
                            <!--ROW 3-->
                            <div class="row form-row">

                                <div class="col-lg-4">
                                    <label for="province">Province</label>
                                    <?php $selProv = old_value('province', $row->province) ?>
                                    <select name="<?= esc('province') ?>" class="form-control mb-1 ntoshi-search" id="province">
                                        <option value="">-- Select Province --</option>
                                        <option value="Eastern Cape" <?= $selProv == 'Eastern Cape' ? 'selected' : '' ?>>Eastern Cape</option>
                                        <option value="KwaZulu Natal" <?= $selProv == 'KwaZulu Natal' ? 'selected' : '' ?>>KwaZulu Natal</option>
                                        <option value="Northern Cape" <?= $selProv == 'Northern Cape' ? 'selected' : '' ?>>Northern Cape</option>
                                        <option value="Western Cape" <?= $selProv == 'Western Cape' ? 'selected' : '' ?>>Western Cape</option>
                                        <option value="Free State" <?= $selProv == 'Free State' ? 'selected' : '' ?>>Free State</option>
                                        <option value="North West" <?= $selProv == 'North West' ? 'selected' : '' ?>>North West</option>
                                        <option value="Mpumalanga" <?= $selProv == 'Mpumalanga' ? 'selected' : '' ?>>Mpumalanga</option>
                                        <option value="Limpopo" <?= $selProv == 'Limpopo' ? 'selected' : '' ?>>Limpopo</option>
                                        <option value="Gauteng" <?= $selProv == 'Gauteng' ? 'selected' : '' ?>>Gauteng</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="country">Country</label>
                                    <input type="text" name="<?= esc('country', $row->country) ?>" value="<?= 'South Africa' ?>" class="form-control mb-1" id="country" readonly>
                                </div>
                                <div class="col-lg-4">
                                    <label for="status">Status</label>
                                    <?php $selStatus = old_value('status', $row->status) ?>
                                    <select name="<?= esc('status') ?>" class="form-control mb-1" id="status">
                                        <option value="Active" <?= $selStatus == 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Suspended" <?= $selStatus == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                        <option value="Inactive" <?= $selStatus == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="Pending" <?= $selStatus == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Blacklisted" <?= $selStatus == 'Blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">UPDATE CLIENT DETAILS</button>
                                    <a href="<?= ROOT ?>/admin/clients" class="btn btn-danger">CANCEL</a>
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