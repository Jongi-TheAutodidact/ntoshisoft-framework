<?php

/**
 * @var string $page_title
 * @var array $data
 * @var string $employee_number
 * @var array $available_users
 * @var array $errors
 */
$this->view('inc/header', $data);
?>
<main class="main" id="main">
    <section class="section p-4">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" id="employee-create" data-offline-table="employees" data-offline-action="insert">
                            <?= displayFormHeaderOnCreate() ?>


                            <h5 class="card-title mb-4">Register New Employee</h5>
                            <input type="hidden" name="employee_number" value="<?= $employee_number ?>">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <?= implode('<br>', $errors) ?>
                                </div>
                            <?php endif; ?>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="user_id" class="form-label">Select User*</label>
                                    <select name="<?= esc('user_id') ?>" class="form-select ntoshi-search" id="user_id" required>
                                        <option value="">-- Choose User --</option>
                                        <?php foreach ($available_users as $user): ?>
                                            <option value="<?= $user->user_id ?>" <?= selected(old_value('user_id'), $user->user_id) ?>><?= esc($user->firstname . ' ' . $user->surname) ?> (<?= esc($user->email) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="position" class="form-label">Position*</label>
                                    <?php $selPosition = old_value('position') ?>
                                    <select name="<?= esc('position') ?>" class="form-select ntoshi-search form-control" id="position" required>
                                        <option value="">-- Select Job Title --</option>
                                        <?php if (!empty(USER_ROLES)): ?>
                                            <?php foreach (USER_ROLES as $title): ?>
                                                <option value="<?= $title ?>" <?= $selPosition == $title ? 'selected' : '' ?>><?= $title ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <input type="text" name="<?= esc('user_role') ?>" value="Subscriber" class="ns-form-control" readonly>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" name="<?= esc('department') ?>"
                                        value="<?= old_value('department') ?>"
                                        class="form-control" id="department">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="hire_date" class="form-label">Hire Date*</label>
                                    <input type="date" name="<?= esc('hire_date') ?>"
                                        value="<?= old_value('hire_date') ?>"
                                        class="form-control" id="hire_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                    <input type="text" name="<?= esc('emergency_contact') ?>"
                                        value="<?= old_value('emergency_contact') ?>"
                                        class="form-control" id="emergency_contact">
                                </div>
                                <div class="col-md-4">
                                    <label for="emergency_phone" class="form-label">Emergency Phone</label>
                                    <input type="tel" name="<?= esc('emergency_phone') ?>"
                                        value="<?= old_value('emergency_phone') ?>"
                                        class="form-control" id="emergency_phone">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="qualifications" class="form-label">Qualifications/Certifications</label>
                                    <textarea name="<?= esc('qualifications') ?>"
                                        class="form-control" id="qualifications"
                                        rows="2"><?= old_value('qualifications') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="<?= esc('notes') ?>"
                                        class="form-control" id="notes"
                                        rows="2"><?= old_value('notes') ?></textarea>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning text-dark">
                                    <i class="bi bi-person-plus-fill me-1"></i> Register Employee
                                </button>
                                <a href="<?= ROOT ?>/admin/employees" class="btn btn-danger">
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