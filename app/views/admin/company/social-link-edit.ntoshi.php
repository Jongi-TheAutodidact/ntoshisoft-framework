<?php
/**
 * @var array $errors
 * @var object $row
 * @var array $data
 */
$this->view('inc/header', $data);
?>

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>

                            <div class="row form-row">
                                <div class="col-lg-12">
                                    <label for="twitter_link">Twitter Link</label>
                                    <input type="text" name="<?= esc('twitter_link') ?>" value="<?= old_value('twitter_link'), $row->twitter_link ? $row->twitter_link : '' ?>" class="form-control mb-1" id="twitter_link">
                                </div>
                                <div class="col-lg-12">
                                    <label for="facebook_link">Facebook Link</label>
                                    <input type="text" name="<?= esc('facebook_link') ?>" value="<?= old_value('facebook_link'), $row->facebook_link ? $row->facebook_link : '' ?>" class="form-control mb-1" id="facebook_link">
                                </div>
                                <div class="col-lg-12">
                                    <label for="tiktok_link">Tiktok Link</label>
                                    <input type="text" name="<?= esc('tiktok_link') ?>" value="<?= old_value('tiktok_link'), $row->tiktok_link ? $row->tiktok_link : '' ?>" class="form-control mb-1" id="tiktok_link">
                                </div>
                                <div class="col-lg-12">
                                    <label for="instagram_link">Instagram Link</label>
                                    <input type="text" name="<?= esc('instagram_link') ?>" value="<?= old_value('instagram_link'), $row->instagram_link ? $row->instagram_link : '' ?>" class="form-control mb-1" id="instagram_link">
                                </div>
                                <div class="col-lg-12">
                                    <label for="linkedin">LinkedIn</label>
                                    <input type="text" name="<?= esc('linkedin') ?>" value="<?= old_value('linkedin'), $row->linkedin ? $row->linkedin : '' ?>" class="form-control mb-1" id="linkedin">
                                </div>
                                <div class="col-lg-12">
                                    <label for="youtube_link">Youtube</label>
                                    <input type="text" name="<?= esc('youtube_link') ?>" value="<?= old_value('youtube_link'), $row->youtube_link ? $row->youtube_link : '' ?>" class="form-control mb-1" id="youtube_link">
                                </div>

                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-success">EDIT DETAILS</button>
                                    <a href="<?= ROOT ?>/admin/social" class="btn btn-danger">CANCEL</a>
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