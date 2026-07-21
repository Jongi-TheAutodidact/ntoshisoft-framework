<?php

/** @var array $data */
/** @var array $company_details */
/** @var array $errors */
?>

<?php $this->view('inc/header', $data) ?>


<div class="frontend-card px-4">
    <div class="card-body">
        <div class="text-center mt-3">
            <img src="<?= ROOT . '/assets/img/logos/logo.png' ?>" width="60%" alt="<?= get_image('', 'logo') ?>">
        </div>
        <hr>
        <h3 class="card-title text-center mb-4">Create Account</h3>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger text-center">
                <?= implode('<br>', $errors);  ?>
            </div>
        <?php endif; ?>

        <form id="register-form" method="POST" class="mb-5" enctype="multipart/form-data">
            <!--USERNAME-->
            <input type="hidden" name="<?= esc('username') ?>">
            <!--USERROLE-->
            <input type="hidden" name="<?= esc('user_role') ?>">
            <!--USERID-->
            <input type="hidden" name="<?= esc('user_id') ?>" value="<?= rand(10001, 99099) ?>">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <label> Profile Image (Upload)<br>
                        <img src="<?= get_image('', 'user')  ?>" class="rounded-circle" width="80px" height="80px" style=" object-fit:cover;cursor:pointer">
                        <input onchange="display_image(this.files[0], event)" type="file" value="<?= old_value('image') ?>" name="<?= esc('image') ?>" class="d-none">
                    </label>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name(s)</label>
                        <input type="text" name="<?= esc('firstname') ?>" class="form-control" value="<?= old_value('firstname') ?>" id="firstname" placeholder="Your First Name" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" name="<?= esc('surname') ?>" class="form-control" value="<?= old_value('surname') ?>" id="surname" placeholder="Your Surname" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="gender">Gender</label>
                        <?php $selGender = old_value('gender') ?>
                        <select name="<?= esc('gender') ?>" class="form-control text-light bg-secondary" id="gender">
                            <option value="">-- Choose Gender --</option>
                            <option value="Male" <?= $selGender == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $selGender == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="<?= esc('password') ?>" value="<?= old_value('password') ?>" class="form-control" id="password" placeholder="Create a password" required>
                </div>
                <div class="col-lg-4">
                    <div class="mb-3">
                        <label for="phone">Phone</label>
                        <input type="text" name="<?= esc('phone') ?>" value="<?= old_value('phone') ?>" placeholder="Your Phone Number" class="form-control" id="phone">
                    </div>
                </div>
            </div>
            <div class="row">
                <hr>
                <p class="mb-1 text-center"><em>Already Registered? <a href="<?= ROOT . '/auth/login' ?>" class="text-decoration-none text-success">Login Here</a></em></p>
                <hr>
            </div>

            <!-- <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                <label class="form-check-label" for="agreeTerms">I agree to the <a href="#" class="text-decoration-none">terms and conditions</a></label>
            </div> -->
            <div class="text-center">
                <button type="submit" class="btn btn-warning text-dark text-center"><i class="bi bi-box-arrow-in-right"></i>REGISTER AS USER</button>
            </div>
        </form>
        <hr class="my-4">
        <div class="text-center">
            <p class="mt-3"><a href="<?= ROOT ?>" class="btn btn-outline-secondary rounded"><i class="bi bi-arrow-left-circle me-1"></i>Back to Site</a></p>
        </div>
    </div>
</div>


<?php $this->view('inc/footer', $data) ?>