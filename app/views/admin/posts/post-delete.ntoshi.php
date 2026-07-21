<?php
    /**
     * @var object $row
     * @var array $errors
     * @var string $page_title
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
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--RECORD(S) DELETED-->
                            <input type="hidden" name="<?= esc('record_deleted') ?>" value="Post = <?= $row->title ?>">
                            <!--USER DELETING RECORD-->
                            <input type="hidden" name="<?= esc('deleted_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <!--DATE RECORD DELETED-->
                            <input type="hidden" name="<?= esc('date_deleted') ?>" value="<?= date('Y-m-d H:i:s') ?>">
                            <!--TABLE-->
                            <input type="hidden" name="<?= esc('from_table') ?>" value="<?= $page_title ?>">

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>

                            <!--Blog Image-->
                            <div class="row form-row">
                                <div class="col-lg-12 text-center">
                                    <label>
                                        <h6 class="text-center" style="cursor:pointer">Click to upload/change image</h6>
                                        <img src="<?= get_image($row->image) ?>" style="width:300px; height:300px; object-fit:cover;cursor:pointer">
                                        <input onchange="display_image(this.files[0], event)" type="file" name="<?= esc('image') ?>" class="d-none">
                                    </label>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="title">Post Title</label>
                                    <div class="form-control mb-1"><?= $row->title ?></div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="content">Content</label>
                                    <div class="form-control mb-1"><?= $row->content ?></div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <label for="author">Author</label>
                                    <div class="form-control mb-1"><?= $row->author ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="category">Category</label>
                                    <div class="form-control mb-1"><?= $row->category ?></div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="form-row">

                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">DELETE POST</button>
                                    <a href="<?= ROOT ?>/admin/posts" class="btn btn-danger">CANCEL</a>
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