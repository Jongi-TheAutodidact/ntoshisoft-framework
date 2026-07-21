<?php
/** @var object $employee */
/** @var array $schedule */
/** @var array $documents */
$this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>

<main class="container-fluid px-4">
    <div class="row my-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-4 page-title">Employee Profile</h3>
                <div>
                    <a href="<?= ROOT ?>/admin/employees" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <a href="<?= ROOT ?>/admin/employee/edit/<?= $employee->id ?>" class="btn btn-warning me-2">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <a href="<?= ROOT ?>/admin/employee/delete/<?= $employee->id ?>" class="btn btn-danger">
                        <i class="bi bi-trash-fill me-1"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Column -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="<?= get_image($employee->image, 'user') ?>"
                        class="rounded-circle mb-3"
                        style="width:150px;height:150px;object-fit:cover"
                        alt="<?= esc($employee->firstname) ?>">

                    <h4><?= esc($employee->firstname . ' ' . $employee->surname) ?></h4>
                    <h5 class="text-primary mb-3"><?= esc($employee->position) ?></h5>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-<?= empty($employee->termination_date) ? 'success' : 'secondary' ?>">
                            <?= empty($employee->termination_date) ? 'Active' : 'Inactive' ?>
                        </span>
                        <span class="badge bg-warning text-dark">EMP NO: <?= esc($employee->employee_number) ?></span>
                    </div>

                    <div class="text-start">
                        <p><i class="bi bi-envelope-fill me-2"></i> <?= esc($employee->email) ?></p>
                        <p><i class="bi bi-telephone-fill me-2"></i> <?= esc($employee->phone) ?></p>
                        <?php if ($employee->department): ?>
                            <p><i class="bi bi-building me-2"></i> <?= esc($employee->department) ?></p>
                        <?php endif; ?>
                        <p><i class="bi bi-calendar-check me-2"></i> Employment Date: <?= date('M j, Y', strtotime($employee->hire_date)) ?></p>
                        <?php if ($employee->termination_date): ?>
                            <p><i class="bi bi-calendar-x me-2"></i> Terminated: <?= date('M j, Y', strtotime($employee->termination_date)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Column -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-light">
                    <h5 class="mb-0">Employment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Emergency Contacts</h6>
                            <?php if ($employee->emergency_contact): ?>
                                <p><strong><?= esc($employee->emergency_contact) ?></strong></p>
                                <p><i class="bi bi-telephone me-2"></i> <?= esc($employee->emergency_phone) ?></p>
                            <?php else: ?>
                                <p class="text-muted">No emergency contacts listed</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6>Qualifications</h6>
                            <?php if ($employee->qualifications): ?>
                                <?= nl2br(esc($employee->qualifications)) ?>
                            <?php else: ?>
                                <p class="text-muted">No qualifications listed</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add these tabs to the existing view -->
            <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button">Schedule</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">Documents</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button">Performance</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Existing profile tab content... -->

                <!-- Schedule Tab -->
                <div class="tab-pane fade" id="schedule">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-light d-flex justify-content-between">
                            <h5>Work Schedule</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editScheduleModal">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                        </div>
                        <div class="card-body">
                            <?php
                                $skills = !empty($employee->skills) ? json_decode($employee->skills, true) : [];
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Morning (8AM-12PM)</th>
                                            <th>Afternoon (1PM-5PM)</th>
                                            <th>Evening (6PM-10PM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
                                            <tr>
                                                <td><strong><?= ucfirst($day) ?></strong></td>
                                                <?php foreach (['morning', 'afternoon', 'evening'] as $shift): ?>
                                                    <td>
                                                        <?= !empty($schedule[$day][$shift]) ?
                                                            '<span class="badge bg-success">Working</span>' :
                                                            '<span class="badge bg-secondary">Off</span>' ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (!empty($schedule['special_notes'])): ?>
                                <div class="alert alert-info mt-3">
                                    <h6>Special Notes:</h6>
                                    <?= nl2br(esc($schedule['special_notes'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-light">
                            <h5>Employee Documents</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= ROOT ?>/admin/upload-document/<?= $employee->id ?>"
                                enctype="multipart/form-data" class="mb-4">
                                <div class="input-group">
                                    <input type="file" name="document" class="form-control" required>
                                    <button type="submit" class="btn btn-warning text-dark">
                                        <i class="bi bi-upload"></i> Upload
                                    </button>
                                </div>
                                <small class="text-muted">Allowed: PDF, JPG, PNG up to 5MB</small>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Upload Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($documents): foreach ($documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <i class="bi bi-file-earmark-text me-2"></i>
                                                        <?= esc($doc) ?>
                                                    </td>
                                                    <td><?= date('M j, Y', filemtime("uploads/employees/$employee->id/docs/$doc")) ?></td>
                                                    <td>
                                                        <a href="<?= ROOT ?>/uploads/employees/<?= $employee->id ?>/docs/<?= $doc ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                            View
                                                        </a>
                                                        <a href="<?= ROOT ?>/admin/employees/delete_document/<?= $employee->id ?>/<?= $doc ?>"
                                                            onclick="return confirm('Delete this document?')"
                                                            class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </a>
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

                <!-- Performance Tab -->
                <div class="tab-pane fade" id="performance">
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            <h5>Performance Evaluation</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h6>Current Rating</h6>
                                            <?php if ($employee->performance_score): ?>
                                                <div class="display-4 text-<?=
                                                                            $employee->performance_score >= 8 ? 'success' : ($employee->performance_score >= 5 ? 'warning' : 'danger') ?>">
                                                    <?= $employee->performance_score ?>/10
                                                </div>
                                                <small>Last evaluated: <?= date('M j, Y', strtotime($employee->last_evaluation_date)) ?></small>
                                            <?php else: ?>
                                                <div class="text-muted">Not evaluated yet</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="<?= ROOT ?>/admin/employees/update_performance/<?= $employee->id ?>">
                                        <div class="mb-3">
                                            <label class="form-label">New Evaluation Score (1-10)</label>
                                            <input type="number" name="score" min="1" max="10" step="0.1"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Evaluation Notes</label>
                                            <textarea name="evaluation_notes" rows="3" class="form-control"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-warning text-dark">
                                            <i class="bi bi-graph-up"></i> Update Performance
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <h6 class="mb-3">Key Skills</h6>
                            <?php $skills = !empty($employee->skills) ? json_decode($employee->skills, true) : ['No Skills Listed']; ?>
                            
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <?php foreach ($skills as $skill): ?>
                                    <span class="badge bg-primary"><?= esc($skill) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Edit Modal -->
            <div class="modal fade" id="editScheduleModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="<?= ROOT ?>/admin/employees/update_schedule/<?= $employee->id ?>">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Work Schedule</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <!-- Schedule editing form similar to display table -->
                                    </table>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Special Notes</label>
                                    <textarea name="special_notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h5 class="mb-0">Administrative Notes</h5>
                </div>
                <div class="card-body">
                    <?php if ($employee->notes): ?>
                        <?= nl2br(esc($employee->notes)) ?>
                    <?php else: ?>
                        <p class="text-muted">No notes recorded</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php $this->view('inc/footer') ?>