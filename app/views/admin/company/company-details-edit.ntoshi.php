<?php
/**
 * @var array $errors
 * @var object $row
 * @var array $data
 */
$this->view('inc/header', $data);
?>
<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12"> 
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <!--CSRF TOKEN-->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--USER UPDATING RECORD-->
                            <input type="hidden" name="<?= esc('updated_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD UPDATED-->
                            <input type="hidden" name="<?= esc('date_updated') ?>" value="<?= date('Y-m-d H:i:s') ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>

                            <div class="row form-row">
                                <label for="about">App Logo:</label>
                                <div class="col-lg-12 text-center">
                                    <label>
                                        <h6 class="text-center" style="cursor:pointer">Click to upload/change image</h6>
                                        <img src="<?= $row->image ? get_image($row->image, 'logo') : '' ?>" style="object-fit:cover;cursor:pointer">
                                        <input onchange="display_image(this.files[0], event)" type="file" name="<?= esc('image') ?>" class="d-none">
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <label for="name">Name:</label>
                                    <input type="text" name="<?= esc('name') ?>" value="<?= old_value('name', $row->name) ?>" class="form-control mb-1" id="company_name">
                                </div>
                                <div class="col-lg-12">
                                    <label for="about">About:</label>
                                    <textarea class="form-control mb-1" name="<?= esc('about') ?>" cols="30" rows="10"><?= old_value('about', $row->about) ?></textarea>
                                </div>
                                <div class="col-lg-12">
                                    <label for="email">Email:</label>
                                    <input type="email" name="<?= esc('email') ?>" value="<?= old_value('email', $row->email) ?>" class="form-control mb-1" id="email">
                                </div>
                                <div class="col-lg-12">
                                    <label for="phone">Phone:</label>
                                    <input type="text" name="<?= esc('phone') ?>" value="<?= old_value('phone', $row->phone) ?>" class="form-control mb-1" id="phone">
                                </div>
                                <div class="col-lg-12">
                                    <label for="address">Address:</label>
                                    <input type="text" name="<?= esc('address') ?>" value="<?= old_value('address', $row->address) ?>" class="form-control mb-1" id="address">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-success">EDIT DETAILS</button>
                                    <a href="<?= ROOT ?>/admin/company" class="btn btn-danger">CANCEL</a>
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
