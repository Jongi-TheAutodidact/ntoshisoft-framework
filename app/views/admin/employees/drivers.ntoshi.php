<?php 
    /**
     * @var string $page_title
     * @var array $data
     * @var array $drivers
     */
    $this->view('inc/header', $data); 
?>

<main class="container-fluid px-4">
    <div class="row my-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title">Drivers Directory</h3>
            </div>
    
            <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 2;">
                <table class="table table-hover align-middle ntoshitable"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Driver Name</th>
                            <th>Department</th>
                            <th>Employee ID</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php if (!empty($drivers)): // show($drivers);die;  ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($drivers as $driver): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= get_image($driver->image, 'user') ?>" 
                                                 class="rounded-circle me-2" 
                                                 style="width:40px;height:40px;object-fit:cover" 
                                                 alt="<?= esc($driver->firstname) ?>">
                                            <div>
                                                <strong><?= esc($driver->firstname . ' ' . $driver->surname) ?></strong>
                                                <div class="text-muted small"><?= esc($driver->email) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc($driver->department) ?: 'N/A' ?></td>
                                    <td><?= esc($driver->employee_number) ?></td>
                                    <td><?= date('M j, Y', strtotime($driver->hire_date)) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            empty($driver->termination_date) ? 'success' : 'secondary' ?>">
                                            <?= empty($driver->termination_date) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= ROOT ?>/admin/employee/detail/<?= $driver->id ?>" 
                                           class="btn btn-sm btn-outline-info me-1"
                                           title="View">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="<?= ROOT ?>/admin/employee/edit/<?= $driver->id ?>" 
                                           class="btn btn-sm btn-outline-warning me-1"
                                           title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= ROOT ?>/admin/employee/delete/<?= $driver->id ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-people-fill fs-1"></i>
                                    <p class="mt-2 mb-0">No driver found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php $this->view('inc/footer') ?>