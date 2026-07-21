<?php

/**
 * @var string $page_title
 * @var array $data
 * @var object $row
 * @var array $rows
 */
$this->view('inc/header', $data)
?>


<main id="main" class="main">

    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?= Util::displayFlash('register_success', 'success') ?>
                        <?= Util::displayFlash('user_update_success', 'success') ?>
                        <?= Util::displayFlash('user_delete_success', 'success') ?>
                        <div class="row my-4">
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3 class="fs-4 page-title"><?= $page_title ?> List</h3>
                                    <a href="<?= ROOT . '/admin/users/pdf' ?>" class="btn btn-danger"><i class="bi bi-download me-2"></i>Download PDF</a>
                                    <a href="<?= ROOT . '/admin/users/new' ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New User</a>
                                </div>
                                
                                <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 1;">
                                    <table class="table ntoshitable table-hover table-striped align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">User Name</th>
                                                <th scope="col">User Role</th>
                                                <th scope="col" class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="customer-table-body">
                                            <?php
                                            $userRows = 1;
                                            if (!empty($rows)) :
                                                foreach ($rows as $row) :

                                            ?>
                                                    <tr>
                                                        <th scope="row"><?= $userRows++ ?></th>
                                                        <td><img src="<?= get_image($row->image, 'user') ?>" alt="<?= $row->firstname . ' Profile Image' ?>" class="rounded-circle me-2" style="width:30px; height:30px; object-fit:cover;"><?= ucwords(strtolower( $row->firstname . ' ' . $row->surname)) ?></td>
                                                        <td><?= strtolower($row->email) ?></td>
                                                        <td><?= $row->username ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($row->user_role) {
                                                                case 'Admin': ?>
                                                                    <span class="badge bg-success">
                                                                    <?php
                                                                    break;
                                                                case 'User': ?>
                                                                        <span class="badge bg-warning">
                                                                        <?php
                                                                        break;
                                                                    case 'Customer': ?>
                                                                            <span class="badge bg-danger">
                                                                            <?php
                                                                            break;

                                                                        default: ?>
                                                                                <span class="badge bg-secondary">
                                                                            <?php
                                                                            break;
                                                                    }
                                                                            ?>
                                                                            <?= $row->user_role ?>
                                                                                </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="<?= ROOT ?>/admin/users/profile/<?= $row->id ?>" class="btn btn-sm btn-outline-info me-1" title="View User"><i class="fa fa-eye"></i></a>
                                                            <a href="<?= ROOT ?>/admin/users/edit/<?= $row->id ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit User"><i class="fa fa-pencil"></i></a>
                                                            <a href="<?= ROOT ?>/admin/users/delete/<?= $row->id ?>" onclick="alert('Are you sure you want to delete this user? This action cannot be reversed. Click \'OK\' to proceed OR \'JUST REFRESH THE PAGE\' to cancel the action!')" class="btn btn-sm btn-outline-danger" title="Delete User"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                            <?php endforeach;
                                            endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>