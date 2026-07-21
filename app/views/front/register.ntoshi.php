<?php

/** @var array $data */
/** @var array $company_details */
/** @var array $errors */
?>

<?php $this->view('inc/header', $data) ?>


<div class="frontend-card px-4">
    <div class="card-body">
        <h3 class="card-title text-center mb-4">Create Account</h3>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger text-center">
                <?= implode('<br>', $errors);  ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="user-new" enctype="multipart/form-data">
            <!--VERIFY TOKEN-->
            <input type="hidden" name="<?= esc('verify_token') ?>" value="<?= md5(rand()) ?>">
            <!--USERID-->
            <input type="hidden" name="<?= esc('user_id') ?>" value="<?= rand(10001, 99099) ?>">
            <!--USER CREATING RECORD-->
            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger text-center col-lg-12">
                    <?= implode('<br>', $errors);  ?>
                </div>
            <?php endif; ?>
            <?= Util::displayFlash('register_error', 'danger') ?>
            <?= Util::displayFlash('email_exists_error', 'danger') ?>
            <?= Util::displayFlash('username_exists_error', 'danger') ?>
            <!--ROW 0-->
            <div class="row form-row">
                <div class="col-lg-12 text-center">
                    <label> Profile Image<br>
                        <img src="<?= get_image('', 'user')  ?>" class="rounded-circle" width="80px" height="80px" style=" object-fit:cover;cursor:pointer">
                        <input onchange="display_image(this.files[0], event)" type="file" value="<?= old_value('image') ?>" name="<?= esc('image') ?>" class="d-none">
                    </label>
                </div>
            </div>
            <hr>
            <!--ROW 1-->
            <div class="row form-row">
                <div class="col-lg-4 mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" name="<?= esc('firstname') ?>" value="<?= old_value('firstname') ?>" class="form-control mb-1" id="firstname">
                </div>
                <div class="col-lg-4 mb-3">
                    <label for="surname">Surname</label>
                    <input type="text" name="<?= esc('surname') ?>" value="<?= old_value('surname') ?>" class="form-control mb-1" id="surname">
                </div>
                <div class="col-lg-4 mb-3">
                    <label for="username">Username</label>
                    <input type="text" name="<?= esc('username') ?>" value="<?= old_value('username') ?>" class="form-control mb-1" id="username">
                </div>
            </div>
            <!--ROW 2-->
            <div class="row form-row">
                <div class="col-lg-4 mb-3">
                    <label for="user_role">User Role</label>
                    <?php
                    switch (basename($_SERVER['REQUEST_URI'])) {
                        case  'create-user': ?>
                            <input type="text" name="<?= esc('user_role') ?>" value="<?= 'Subscriber' ?>" class="form-control mb-1" id="user_role" readonly>
                        <?php
                            break;

                        default: ?>

                            <?php $selUserRole = old_value('user_role') ?>
                            <select name="<?= esc('user_role') ?>" id="user_role" class="form-control mb-1">
                                <option value="Select Role">--Select User Role--</option>
                                <?php if (!empty(USER_ROLES)): foreach (USER_ROLES as $role): ?>
                                        <option value="<?= $role ?>" <?= $selUserRole == $role ? 'selected' : '' ?>><?= $role ?></option>
                                    <?php endforeach;
                                else: ?>
                                    <option value="Admin" <?= $selUserRole == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="User" <?= $selUserRole == 'User' ? 'selected' : '' ?>>User</option>
                                    <option value="Customer" <?= $selUserRole == 'Customer' ? 'selected' : '' ?>>Customer</option>
                                <?php endif ?>
                            </select>
                    <?php
                            break;
                    }
                    ?>

                </div>
                <div class="col-lg-4 mb-3">
                    <label for="password">Password</label>
                    <input type="password" name="<?= esc('password') ?>" value="<?= old_value('password') ?>" class="form-control mb-1" id="password">
                </div>
                <div class="col-lg-4 mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" name="<?= esc('email') ?>" value="<?= old_value('email') ?>" class="form-control mb-1" id="email">
                </div>
            </div>
            <!--ROW 3-->
            <div class=" row form-row">
                <div class="col-lg-6 mb-3">
                    <label for="phone">Phone</label>
                    <input type="text" name="<?= esc('phone') ?>" value="<?= old_value('phone') ?>" class="form-control mb-1" id="phone">
                </div>
                <div class="col-lg-6 mb-3">
                    <label for="gender">Gender</label>
                    <select name="<?= esc('gender') ?>" class="form-control mb-1" id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="d-grid gap-2 col-lg-12">
                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">CREATE NEW USER</button>
                    <div class="alert alert-success" id="alert" style="display: none;"></div>
                    <a href="<?= ROOT ?>/admin/users" class="btn btn-danger">CANCEL</a>
                </div>
            </div>
        </form>
        <hr class="my-4">
        <div class="text-center">
            <p class="mt-3"><a href="<?= ROOT ?>" class="btn btn-outline-secondary rounded"><i class="bi bi-arrow-left-circle me-1"></i>Back to Site</a></p>
        </div>
    </div>
</div>


<?php $this->view('inc/footer', $data) ?>