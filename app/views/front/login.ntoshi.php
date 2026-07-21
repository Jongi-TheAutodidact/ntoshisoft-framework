<?php

/** @var array $data */
/** @var array $company_details */
/** @var array $errors */
/** @var ?string $sess_email */
?>

<?php $this->view('inc/header', $data) ?>


<div class="frontend-card auth-card">
    <div class="card-body mx-5">
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger text-center">
                <?= implode('<br>', $errors);  ?>
            </div>
        <?php endif; ?>
        <form method="post" id="login-form">
            <div class="mb-3">
                <label for="email" class="form-label">Username/Email</label>
                <input type="text" name="<?= esc('email') ?>" value="<?php if (!empty($sess_email)) {
                                                                            echo $sess_email;
                                                                        } elseif (isset($_COOKIE['remember_email'])) {
                                                                            echo $_COOKIE['remember_email'];
                                                                        }  ?>" class="form-control" id="email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="<?= esc('password') ?>" class="form-control" id="password" placeholder="Password">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember me</label>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-warning text-dark text-center"><i class="bi bi-box-arrow-in-right"></i>LOGIN</button>
            </div>
        </form>
        <hr class="my-4">
        <div class="text-center">            
            <p class="mt-3"><a href="<?= ROOT ?>" class="btn btn-outline-secondary rounded"><i class="bi bi-arrow-left-circle me-1"></i>Back to Site</a></p>
        </div>
    </div>
</div>


<?php $this->view('inc/footer', $data) ?>