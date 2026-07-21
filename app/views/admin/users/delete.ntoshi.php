<?php

/**
 * @var string $page_title
 * @var array $data
 * @var object $row
 */
$this->view('inc/header', $data)
?>

<main id="main" class="main">

    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                       
                        <form method="post">
                            <!--CSRF TOKEN-->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--RECORD(S) DELETED-->
                            <input type="hidden" name="<?= esc('record_deleted') ?>" value="User = <?= $row->firstname . ' ' . $row->surname ?>">
                            <!--USER DELETING RECORD-->
                            <input type="hidden" name="<?= esc('deleted_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD DELETED-->
                            <input type="hidden" name="<?= esc('date_deleted') ?>" value="<?= date('Y-m-d H:i:s') ?>"> 
                            <!--TABLE-->
                            <input type="hidden" name="<?= esc('from_table') ?>" value="<?= $page_title ?>">
                            <!--User Profile Image-->
                            <div class="row form-row">
                                <div class="col-lg-12 text-center">
                                    <label>
                                        <label for="image"> Profile Image</label> <br>
                                        <img src="<?= old_value(get_image($row->image), get_image($row->image, 'user'))  ?>" class="rounded-circle" width="80px" height="80px" style=" object-fit:cover">
                                    </label>
                                </div>
                            </div>
                            <hr>
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <div class="form-control mb-1"><?= $row->firstname ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-control mb-1"><?= $row->surname ?></div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <div class="form-control mb-1"><?= $row->username ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-control mb-1"><?= $row->email ?></div>
                                </div>
                            </div>
                            <div class=" row form-row">
                                <div class="col-lg-6">
                                    <div class="form-control mb-1">Password Reducted</div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-control mb-1"><?= $row->user_role ?></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">DELETE USER</button>
                                    <a href="<?= ROOT ?>/admin/users" class="btn btn-danger">CANCEL</a>
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