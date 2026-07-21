<?php
    /**
     * @var array $categories
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
                        <?php if (user('user_role') == 'Admin' || user('user_role') == 'Manager') : ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="fs-4 page-title">Categories List</h3>
                                <a href="<?= ROOT . '/admin/category/create' ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Category</a>
                            </div>
                            <hr>
                        <?php endif; ?>
                        <?= Util::displayFlash('category_create_success', 'success') ?>
                        <?= Util::displayFlash('category_updated_success', 'success') ?>
                        <?= Util::displayFlash('category_deleted_success', 'success') ?>
                        <div class="row">
                            <!-- Table with stripped rows -->
                            <div class="table-responsive">
                                <table class="table ntoshitable table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Category Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $userRows = 1;
                                        if (!empty($categories)) :
                                            foreach ($categories as $row) :
                                        ?>
                                                <tr>
                                                    <th><?= $userRows++ ?></th>
                                                    <td><?= $row->cat_name ?></td>
                                                    <td>
                                                        <div class="text-center d-flex">
                                                            <a href="<?= ROOT ?>/admin/categories/edit/<?= $row->id ?>"><i class="bi bi-pencil-square m-2 text-primary"></i></a>
                                                            <a href="<?= ROOT ?>/admin/categories/delete/<?= $row->id ?>" onclick="alert('Are you sure you want to delete this record? This action cannot be reversed. Click \'OK\' to proceed OR \'JUST REFRESH THE PAGE\' to cancel the action!')"><i class="bi bi-trash m-2 text-danger "></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- End Table -->
                        </div>
                    </div>
                </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>