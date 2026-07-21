<?php
    /**
     * @var array $errors
     * @var array $categories
     * @var int|string $preselect_cat_id
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
                        <form method="POST" action="" enctype="multipart/form-data">
                            <!--USER CREATING RECORD-->
                           <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>
                            <?php Util::displayFlash('category_create_success','success') ?>
                            <!--Blog Image-->
                            <div class="row form-row">
                                <div class="col-lg-12 text-center">
                                    <label>
                                        <h6 class="text-center" style="cursor:pointer">Click to upload/change image</h6>
                                        <img src="<?= get_image() ?>"
                                            style="width:300px; height:300px; object-fit:cover;cursor:pointer">
                                        <input onchange="display_image(this.files[0], event)" type="file"
                                            name="<?= esc('image') ?>" class="d-none">
                                    </label>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="title">Post Title</label>
                                    <input type="text" name="<?= esc('title') ?>" value="<?= old_value('title') ?>"
                                        class="form-control mb-1" id="title">
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="content">Content</label>
                                    <textarea name="<?= esc('content') ?>" class="form-control mb-1" id="post_content"
                                        cols="30" rows="10"><?= old_value('content') ?></textarea>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <label for="author">Author</label>
                                    <!-- BlogPost Author - Current User -->
                                    <input type="text" name="<?= esc('author') ?>" class="form-control mb-1"
                                        value="<?= user('firstname') . ' ' . user('surname') ?>" id="author" readonly>
                                </div>
                                <div class="col-lg-6">
                                    <label for="category">Category</label>
                                    <?php $selCat = old_value('category', $preselect_cat_id) ?>

                                    <select name="<?= esc('category') ?>" class="form-control mb-1" id="category">
                                        <option value="Select category">--Select category--</option>
                                        <?php if ($categories) :
                                            foreach ($categories as $catRow) : ?>
                                                <option value="<?= $catRow->sn ?>" <?= $selCat == $catRow->sn ? 'selected' : '' ?>><?= $catRow->cat_name ?></option>
                                        <?php endforeach;
                                        endif; ?>
                                    </select> <span style="font-size: 12px;"><a href="<?= ROOT . '/admin/posts/create-category' ?>"><i class="bi bi-plus-circle"></i> Create New Category</a> or select one from the dropdown</span>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="form-row"> 
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-warning text-dark">CREATE NEW POST</button>
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