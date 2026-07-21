<?php
/** @var array $errors */
/** @var object $row */
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
                        <form method="POST" id="employee-edit">
                           <?= displayFormHeaderOnUpdate() ?>
                            <input type="hidden" name="employee_number" value="<?= $row->employee_number ?>" readonly>
                            <h5 class="card-title mb-4">Edit Employee: <?= esc($row->firstname . ' ' . $row->surname) ?></h5>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <?= implode('<br>', $errors) ?>
                                </div>
                            <?php endif; ?>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="user_id" class="form-label">Employee Name</label>
                                    <input type="text" name="<?= esc('user_id') ?>" id="user_id"  class="form-control" value="<?= $row->full_name ?>" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="position" class="form-label">Position*</label>
                                    <?php $selPosition = old_value('position', $row->position) ?>
                                    <select name="<?= esc('position') ?>" class="form-select ntoshi-search form-control" id="position" required>
                                        <option value="">-- Select Position --</option>
                                        <option value="Receptionist" <?= $selPosition == 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                                        <option value="Driver" <?= $selPosition == 'Driver' ? 'selected' : '' ?>>Driver</option>
                                        <option value="Admin Clerk" <?= $selPosition == 'Admin Clerk' ? 'selected' : '' ?>>Admin Clerk</option>
                                        <option value="Accountant" <?= $selPosition == 'Accountant' ? 'selected' : '' ?>>Accountant</option>
                                        <option value="Manager" <?= $selPosition == 'Manager' ? 'selected' : '' ?>>Manager</option>
                                        <option value="Other" <?= $selPosition == 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" name="<?= esc('department') ?>"
                                        value="<?= old_value('department', $row->department) ?>"
                                        class="form-control" id="department">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="hire_date" class="form-label">Hire Date*</label>
                                    <input type="date" name="<?= esc('hire_date') ?>"
                                        value="<?= old_value('hire_date', $row->hire_date) ?>"
                                        class="form-control" id="hire_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                    <input type="text" name="<?= esc('emergency_contact') ?>"
                                        value="<?= old_value('emergency_contact', $row->emergency_contact) ?>"
                                        class="form-control" id="emergency_contact">
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_phone" class="form-label">Emergency Phone</label>
                                    <input type="tel" name="<?= esc('emergency_phone') ?>"
                                        value="<?= old_value('emergency_phone', $row->emergency_phone) ?>"
                                        class="form-control" id="emergency_phone">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="qualifications" class="form-label">Qualifications/Certifications</label>
                                    <textarea name="<?= esc('qualifications') ?>"
                                        class="form-control" id="qualifications"
                                        rows="2"><?= old_value('qualifications', $row->qualifications) ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="<?= esc('notes') ?>"
                                        class="form-control" id="notes"
                                        rows="2"><?= old_value('notes', $row->notes) ?></textarea>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-save-fill me-1"></i> Save Changes
                                </button>
                                <a href="<?= ROOT ?>/admin/employee/detail/<?= $row->id ?>" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php $this->view('inc/footer') ?>