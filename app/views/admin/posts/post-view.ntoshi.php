<?php
    /**
     * @var object $single_post
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

                        <form>
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">

                            <!--Blog Image-->
                            <div class="row form-row">
                                <div class="col-lg-12 text-center">
                                    <label>
                                        <img src="<?= get_image($single_post->image) ?>" style="width:100%; object-fit:cover">
                                    </label>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="title">Post Title</label>
                                    <div class="form-control mb-1"><?= $single_post->title ?></div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="content">Content</label>
                                    <div class="form-control mb-1"><?= $single_post->content ?></div>
                                </div>
                            </div>
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <label for="author">Author</label>
                                    <div class="form-control mb-1"><?= $single_post->author ?></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="category">Category</label>
                                    <div class="form-control mb-1"><?= $single_post->cat_name ?></div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="form-row">

                                <div class="d-grid gap-2 col-lg-12">
                                    <a href="<?= ROOT ?>/admin/posts" class="btn btn-danger">BACK TO POSTS</a>
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