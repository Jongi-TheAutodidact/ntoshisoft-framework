<?php
/**
 * @var array $errors
 * @var array $op_hours
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
                        <form method="POST" action="">
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
                                <div class="col-lg-12">
                                    <label for="mon">Monday:</label>
                                    <input type="text" name="mon" value="<?= old_value('mon'), $op_hours[0]->mon ?>" class="form-control mb-1" id="op_hour_mon">
                                </div>
                                <div class="col-lg-12">
                                    <label for="tue">Tuesday:</label>
                                    <input type="text" name="tue" value="<?= old_value('tue'), $op_hours[0]->tue ?>" class="form-control mb-1" id="op_hour_tue">
                                </div>
                                <div class="col-lg-12">
                                    <label for="wed">Wednesday:</label>
                                    <input type="text" name="wed" value="<?= old_value('wed'), $op_hours[0]->wed ?>" class="form-control mb-1" id="op_hour_wed">
                                </div>
                                <div class="col-lg-12">
                                    <label for="thu">Thursday:</label>
                                    <input type="text" name="thu" value="<?= old_value('thu'), $op_hours[0]->thu ?>" class="form-control mb-1" id="op_hour_thu">
                                </div>
                                <div class="col-lg-12">
                                    <label for="fri">Friday:</label>
                                    <input type="text" name="fri" value="<?= old_value('fri'), $op_hours[0]->fri ?>" class="form-control mb-1" id="op_hour_fri">
                                </div>
                                <div class="col-lg-12">
                                    <label for="sat">Saturday:</label>
                                    <input type="text" name="sat" value="<?= old_value('sat'), $op_hours[0]->sat ?>" class="form-control mb-1" id="op_hour_sat">
                                </div>
                                <div class="col-lg-12">
                                    <label for="sun">Sunday:</label>
                                    <input type="text" name="sun" value="<?= old_value('sun'), $op_hours[0]->sun ?>" class="form-control mb-1" id="op_hour_sun">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="d-grid gap-2 col-lg-12">
                                    <button type="submit" class="btn btn-<?= 'success' ?>">EDIT DETAILS</button>
                                    <a href="<?= ROOT ?>/admin/hours" class="btn btn-danger">CANCEL</a>
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