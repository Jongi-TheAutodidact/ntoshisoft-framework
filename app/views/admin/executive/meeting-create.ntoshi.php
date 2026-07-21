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
                        <form method="POST" action="" id="debtor-new" enctype="multipart/form-data">
                            <!--CSRF TOKEN-->
                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <!--VERIFY TOKEN-->
                            <input type="hidden" name="<?= esc('verify_token') ?>" value="<?= md5(rand()) ?>">
                            <!--USER ID-->
                            <input type="hidden" name="<?= esc('user_id') ?>" value="<?= user('user_id') ?>">
                            <!--MEETING ID-->
                            <input type="hidden" name="<?= esc('meeting_id') ?>" value="<?= bin2hex(random_bytes(5)) ?>">
                            <!--USER CREATING RECORD-->
                            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>

                            <!--ROW 1-->
                            <div class="row form-row">
                                <div class="col-lg-6">
                                    <label for="meeting_title">Meeting Title</label>
                                    <input type="text" name="<?= esc('meeting_title') ?>" value="<?= old_value('meeting_title') ?>" class="form-control mb-1" id="meeting_title">
                                </div>
                                <div class="col-lg-6">
                                    <label for="scheduled_for">Scheduled For</label>
                                    <input type="datetime-local" name="<?= esc('scheduled_for') ?>" value="<?= old_value('scheduled_for') ?>" class="form-control mb-1" id="scheduled_for">
                                </div>
                             
                            </div>
                            <!--ROW 2-->
                            <div class="row form-row">
                                <label for="notes">Notes</label>
                                <textarea name="<?= esc('notes') ?>" class="form-control mb-1" id="notes"><?= old_value('notes') ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-outline-<?= THEME_COLOR ?>">CREATE NEW MEETING</button>
                                    <a href="<?= ROOT ?>/admin/meetings" class="btn btn-danger">CANCEL</a>
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