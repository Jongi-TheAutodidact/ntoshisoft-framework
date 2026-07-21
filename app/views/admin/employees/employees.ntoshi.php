<?php 
    /**
     * @var string $page_title
     * @var array $data
     * @var array $employees
     */
    $this->view('inc/header', $data); 
?>
    <div class="row my-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title">Employee Directory</h3>
                <a href="<?= ROOT ?>/admin/employee/create" class="btn btn-warning text-dark">
                    <i class="bi bi-plus-circle me-1"></i> Add Employee
                </a>
            </div>
            
            <?= Util::displayFlash('employee_created', 'success') ?>
            <?= Util::displayFlash('employee_updated', 'success') ?>
            <?= Util::displayFlash('employee_deleted', 'success') ?>
            
            <div class="table-responsive bg-body-tertiary p-3 rounded shadow-sm animated-card" style="--animation-order: 2;">
                <table class="table table-hover align-middle ntoshitable"> 
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Employee ID</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php if (!empty($employees)):  ?>
                            <?php $counter = 1; ?>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= get_image($emp->image, 'user') ?>" 
                                                 class="rounded-circle me-2" 
                                                 style="width:40px;height:40px;object-fit:cover" 
                                                 alt="<?= esc($emp->full_name) ?>">
                                            <div>
                                                <strong><?= esc($emp->full_name) ?></strong>
                                                <div class="text-muted small"><?= esc($emp->email) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc($emp->position) ?></td>
                                    <td><?= esc($emp->department) ?: 'N/A' ?></td>
                                    <td><?= esc($emp->employee_number) ?></td>
                                    <td><?= date('M j, Y', strtotime($emp->hire_date)) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            empty($emp->termination_date) ? 'success' : 'secondary' ?>">
                                            <?= empty($emp->termination_date) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= ROOT ?>/admin/employee/detail/<?= $emp->id ?>" 
                                           class="btn btn-sm btn-outline-info me-1"
                                           title="View">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="<?= ROOT ?>/admin/employee/edit/<?= $emp->id ?>" 
                                           class="btn btn-sm btn-outline-warning me-1"
                                           title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="<?= ROOT ?>/admin/employee/delete/<?= $emp->id ?>" 
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
                                    <p class="mt-2 mb-0">No employees found</p>
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