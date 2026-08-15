<?php
/** @var array $errors */
/** @var int|string|null $preselect_user_id */
/** @var array $users */
$this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body mb-5">
                        <form method="POST" action="" id="user-new" enctype="multipart/form-data" data-offline-table="clients" data-offline-action="insert">

                            <!-- CSRF TOKEN -->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">

                            <!-- VERIFY TOKEN -->
                            <input type="hidden" name="<?= esc('verify_token') ?>" value="<?= md5(rand()) ?>">

                            <!-- CREATED BY -->
                            <?php if (user('user_id') == basename($_GET['url'])): ?>
                                <input type="hidden" name="created_by" value="Self-Registration">
                            <?php else: ?>
                                <input type="hidden" name="created_by" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <?php endif; ?>

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors); ?>
                                </div>
                            <?php endif; ?>

                            <?= Util::displayFlash('email_exists_error', 'danger') ?>
                            <?= Util::displayFlash('username_exists_error', 'danger') ?>
                            <?= Util::displayFlash('user_register_success', 'success') ?>

                            <!-- ROW 1 -->
                            <div class="row form-row">
                                <!-- User ID -->
                                <div class="col-lg-6 my-2">
                                    <label for="user_id">User</label>
                                    <?php if (user('user_id') == basename($_GET['url'])): ?>
                                        <!-- Locked for onboarding -->
                                        <input type="text" name="user_id" class="form-control bg-body-tertiary text-light mb-1" value="<?= user('user_id') ?>" readonly>

                                    <?php else: ?>
                                        <?php $selUserId = old_value('user_id', $preselect_user_id) ?>
                                        <select name="user_id" class="form-control mb-1 ntoshi-search" id="user_id">
                                            <option value="">-- Select User --</option>
                                            <?php if (!empty($users)): foreach ($users as $u): ?>
                                                    <option value="<?= $u->user_id ?>" <?= $selUserId == $u->user_id ? 'selected' : '' ?>><?= $u->firstname . ' ' . $u->surname ?>
                                                    </option>
                                            <?php endforeach;
                                            endif ?>
                                        </select>
                                        <?php if (user('user_role') != 'Client' || user('user_role') != 'User'): ?>
                                            <span style="font-size: 12px;">
                                                <a href="<?= ROOT . '/admin/client/create-user' ?>">
                                                    <i class="bi bi-plus-circle"></i> Create New user
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- ID Number -->
                                <div class="col-lg-6 my-2">
                                    <label for="identity_number">ID Number</label>
                                    <input type="text" name="identity_number" value="<?= old_value('identity_number', user('user_id') == basename($_GET['url']) ? user('identity_number') : '') ?>" class="form-control mb-1" id="identity_number" placeholder="e.g. 6303215854087" <?= user('user_id') == basename($_GET['url']) && user('identity_number') ? 'readonly' : '' ?>>
                                </div>
                            </div>

                            <!-- ROW 2 -->
                            <div class="row form-row">
                                <div class="col-lg-4 my-2">
                                    <label for="address">Street Address</label>
                                    <input type="text" name="address" value="<?= old_value('address') ?>" class="form-control mb-1" id="address">
                                </div>
                                <div class="col-lg-4 my-2">
                                    <label for="city">City</label>
                                    <input type="text" name="city" value="<?= old_value('city') ?>" class="form-control mb-1" id="city">
                                </div>
                                <div class="col-lg-4 my-2">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="number" name="postal_code" value="<?= old_value('postal_code') ?>" class="form-control mb-1" id="postal_code">
                                </div>
                            </div>

                            <!-- ROW 3 -->
                            <div class="row form-row">
                                <div class="col-lg-4 my-2">
                                    <label for="province">Province</label>
                                    <?php $selProv = old_value('province') ?>
                                    <select name="province" class="form-control mb-1" id="province">
                                        <option value="">-- Select Province --</option>
                                        <?php

                                        foreach (PROVINCES as $prov): ?>
                                            <option value="<?= $prov ?>" <?= $selProv == $prov ? 'selected' : '' ?>><?= $prov ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-lg-4 my-2">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" value="South Africa" class="form-control mb-1" id="country" readonly>
                                </div>

                                <!-- Status -->
                                <div class="col-lg-4 my-2">
                                    <label for="status">Status</label>
                                    <?php if (user('user_id') == basename($_GET['url'])): ?>
                                        <input type="text" name="<?= esc('status') ?>" class="form-control mb-1" value="Assessment" readonly>
                                    <?php else: ?>
                                        <?php $selStatus = old_value('status') ?>
                                        <select name="status" class="form-control mb-1" id="status">
                                            <option value="Active" <?= $selStatus == 'Active' ? 'selected' : '' ?>>Active</option>
                                            <option value="Suspended" <?= $selStatus == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                            <option value="Inactive" <?= $selStatus == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                            <option value="Pending" <?= $selStatus == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Blacklisted" <?= $selStatus == 'Blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- ROW 4 -->
                            <div class="row form-row">
                                <div class="col-lg-4 my-2">
                                    <label for="source_of_funds">Source Of Funds</label>
                                    <?php $selsrc_funds = old_value('source_of_funds') ?>
                                    <select name="source_of_funds" class="form-control mb-1" id="source_of_funds">
                                        <option value="">-- Select Source --</option>
                                        <?php
                                        foreach (SOURCE_OF_FUNDS as $src_funds): ?>
                                            <option value="<?= $src_funds ?>" <?= $selsrc_funds == $src_funds ? 'selected' : '' ?>><?= $src_funds ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-lg-4 my-2">
                                    <label for="nationality">Nationality</label>
                                    <input type="text" name="nationality" value="<?= old_value('nationality') ?>" class="form-control mb-1" id="nationality" placeholder="e.g. South African">
                                </div>

                                <!-- Status -->
                                <div class="col-lg-4 my-2">
                                    <label for="marital_status">Marital Status</label>
                                    <?php $selStatus = old_value('marital_status') ?>
                                    <select name="marital_status" class="form-control mb-1" id="marital_status">
                                        <option value="">-- Select Status --</option>
                                        <option value="Single" <?= $selStatus == 'Single' ? 'selected' : '' ?>>Single</option>
                                        <option value="Married" <?= $selStatus == 'Married' ? 'selected' : '' ?>>Married</option>
                                        <option value="Divorced" <?= $selStatus == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                                    </select>
                                </div>
                            </div>

                            <!--ROW 5-->
                            <div class="row form-row">
                                <div class="col-lg-6 my-2">
                                    <label for="prem_col_date">Premium Collection Day</label>
                                    <input type="text" name="prem_col_date" value="<?= old_value('prem_col_date') ?>" class="form-control mb-1" id="prem_col_date" placeholder="e.g. 1st, 15th, 20th, 31st">
                                </div>
                                <div class="col-lg-6 my-2">
                                    <label for="notes">Notes (Optional)</label>
                                    <textarea name="<?= esc('notes') ?>" class="form-control" id="notes" rows="3"><?= old_value('notes') ?></textarea>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-row mt-3">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">
                                        <?= user('user_id') == basename($_GET['url']) ? 'COMPLETE CLIENT APPLICATION' : 'CREATE NEW CLIENT' ?>
                                    </button>
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

<?php $this->view('inc/footer') ?>


