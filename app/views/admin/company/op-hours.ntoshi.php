<?php
/**
 * @var array $op_hours
 * @var object $row
 * @var array $data
 */
$this->view('inc/header', $data);
?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main id="main" class="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?= Util::displayFlash('ophours_update_success', 'success') ?>
                        <div class="row mt-3">
                            <!-- Table with stripped rows -->
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    $userRows = 1;
                                    if (!empty($op_hours)) :
                                        foreach ($op_hours as $row) :
                                    ?>
                                            <tr>
                                                <th scope="col">Monday:</th>
                                                <td><?= $row->mon ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Tuesday:</th>
                                                <td><?= $row->tue ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Wednesday:</th>
                                                <td><?= $row->wed ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Thursday:</th>
                                                <td><?= $row->thu ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Friday:</th>
                                                <td><?= $row->fri ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Saturday:</th>
                                                <td><?= $row->sat ?></td>
                                            </tr>
                                            <tr>
                                                <th scope="col">Sunday:</th>
                                                <td><?= $row->sun ?></td>
                                            </tr>
                                            <?php
                                            switch ($_SESSION['userRole']) {
                                                case 'Admin':
                                                case 'Doctor':
                                                case 'Sister':
                                            ?>
                                                    <tr>
                                                        <th scope="col">Action:</th>
                                                        <td><a href="<?= ROOT ?>/admin/hours/edit/<?= $row->id ?>"><i class="bi bi-pencil-square m-2"></i></a></td>
                                                    </tr>
                                            <?php break;

                                                default:

                                                    break;
                                            }
                                            ?>

                                        <?php endforeach;
                                    else :  ?>
                                        <div class="alert alert-danger text-center">
                                            No records found
                                        </div>
                                    <?php endif;
                                    ?>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>