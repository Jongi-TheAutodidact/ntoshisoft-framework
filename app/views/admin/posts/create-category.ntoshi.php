<?php
    /**
     * @var array $errors
     * @var array $data
     */
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
                            <!--SN-->
                            <input type="hidden" name="<?= esc('sn') ?>" value="<?= rand(1001, 9099) ?>">
                            <!--CSRF TOKEN-->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--USER CREATING RECORD-->
                            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>
                            

                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="cat_name">Category Name</label>
                                    <input type="text" name="<?= esc('cat_name') ?>" value="<?= old_value('cat_name') ?>" class="form-control mb-1" id="cat_name">
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">CREATE NEW CATEGORY</button>
                                    <a href="<?= ROOT ?>/admin/categories" class="btn btn-danger">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>