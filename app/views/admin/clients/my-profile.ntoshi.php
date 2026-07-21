<?php
/** @var object $client_profile */
/** @var array $payments */
/** @var array $documents */
/** @var array $client_notes */
/** @var array $errors */
$this->view('inc/header', $data); ?>
<div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
    <?php $this->view('inc/welcome', $data); ?>
</div>
<!-- Page Content -->
<main class="container-fluid px-4">
    <!-- Client Profile and Quick Stats -->
    <div class="row g-4 my-3">
        <div class="col-lg-4 col-md-6">
            <div class="p-3 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center text-center" style="--animation-order: 1;">
                <img src="<?= get_image($client_profile->image, 'user') ?>" alt="<?= $client_profile->firstname . ' Profile Image' ?>" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--accent-color);">
                <h4 class="mb-1"><?= $client_profile->firstname . ' ' . $client_profile->surname ?></h4>
                <p class="text-muted mb-2"><?= $client_profile->email ?></p>
                <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i><?= $client_profile->phone ?></p>
                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?= $client_profile->address ?></p>
                <p class="mb-1"><i class="bi bi-buildings me-2"></i><?= $client_profile->city ?></p>
                <p class="mb-1"><i class="bi bi-buildings-fill me-2"></i><?= $client_profile->province ?></p>
                <p><span class="badge bg-info text-dark fs-6 my-2"> Gender: <?= $client_profile->gender ?></span></p>
                <span class="badge bg-success fs-6 my-2"><?= $client_profile->status ?></span>

            </div>
        </div>
        <div class="col-lg-8 col-md-6">
            <div class="row g-4">

                <div class="col-lg-6">
                    <div class="p-3 bg-body-tertiary shadow-sm d-flex justify-content-between align-items-center rounded animated-card" style="--animation-order: 4;">
                        <div>
                            <p class="fs-5 mb-1">Date of Birth</p>
                            <h5 style="font-size:14px !important" class="fs-4"><?= $client_profile->identity_number ? extractBirthDateFromSAID($client_profile->identity_number)->format('D F j, Y') : 'Unknown' ?></h5>
                        </div>
                        <i class="bi bi-calendar-check fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-3 bg-body-tertiary shadow-sm d-flex justify-content-between align-items-center rounded animated-card" style="--animation-order: 5;">
                        <div>
                            <p class="fs-5 mb-1">ID Number</p>
                            <h5 style="font-size:14px !important" class="fs-4"><?= $client_profile->identity_number ? $client_profile->identity_number : 'Not Provided On registration' ?></h5>
                        </div>
                        <i class="bi bi-123 fs-1 primary-text border rounded-full secondary-bg p-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Orders, Invoices, Payments -->
    <div class="row my-4">
        <div class="col">
            <div class="bg-body-tertiary p-3 p-md-4 rounded shadow-sm animated-card" style="--animation-order: 6;">
                <ul class="nav nav-tabs nav-pills flex-column flex-sm-row" id="customerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-tab-pane" type="button" role="tab" aria-controls="payments-tab-pane" aria-selected="true"><i class="bi bi-credit-card-2-front-fill me-2"></i>Payments</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button" role="tab" aria-controls="documents-tab-pane" aria-selected="false"><i class="bi bi-receipt-cutoff me-2"></i>Documents</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes-tab-pane" type="button" role="tab" aria-controls="notes-tab-pane" aria-selected="false"><i class="bi bi-journal-text me-2"></i>Notes & Activity</button>
                    </li>
                </ul>
                <div class="tab-content pt-3" id="customerTabsContent">
                    <div class="tab-pane fade" id="payments-tab-pane" role="tabpanel" aria-labelledby="payments-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-3">Premium Payment History</h4>
                            <a href="<?= ROOT . '/admin/payment/create/' . $client_profile->id ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Payment</a>
                        </div>
                        <?php if ($payments): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped ntoshitable align-middle">
                                    <thead>
                                        <tr>
                                            <th>SN#</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $tableRow = 1;
                                        foreach ($payments as $pay): ?>
                                            <tr>
                                                <td><?= $tableRow++ ?></td>
                                                <td><?= esc($pay->payment_date) ?></td>
                                                <td><?= esc($pay->amount) ?></td>
                                                <td><?= esc($pay->paid_via) ?></td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger text-center">
                                No payment found!
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="tab-pane fade" id="documents-tab-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fs-4 page-title">Documents</h3>
                            <a href="<?= ROOT . '/admin/document/upload/' . $client_profile->id ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Upload New Document</a>
                        </div>
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table class="table table-striped ntoshitable" id="dep-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Document Title</th>
                                            <th>File type</th>
                                            <th>Category</th>
                                            <th>File Size</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $counter = 1; ?>
                                        <?php if (!empty($documents)): foreach ($documents as $doc): ?>
                                                <tr>
                                                    <td><?= $counter++ ?></td>
                                                    <td><?= esc($doc->title) ?></td>
                                                    <td><?= esc($doc->file_type) ?></td>
                                                    <td><?= esc($doc->category) ?></td>
                                                    <td><?= formatSizeUnits($doc->file_size) ?></td>
                                                    <td><?= esc($doc->status) ?></td>

                                                    <td>
                                                        <div class="text-center d-flex gap-2 justify-content-center">
                                                            <a href="<?= ROOT ?>/admin/document/edit/<?= $doc->id ?>" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                            <a href="<?= ROOT ?>/admin/document/delete/<?= $doc->id ?>"
                                                                onclick="return confirm('Are you sure?')"
                                                                class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <div class="alert alert-danger text-center">No documents were uploaded for this client!</div>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="tab-pane fade" id="notes-tab-pane" role="tabpanel" aria-labelledby="notes-tab" tabindex="0">
                        <h4 class="mb-3">Notes & Activity Log</h4>
                        <?= Util::displayFlash('note_register_success', 'success') ?>

                        <form method="POST">

                            <input type="hidden" name="<?= esc('csrf_token') ?>" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="<?= esc('created_by') ?>" value="<?= user('firstname') . ' ' . user('surname') ?>">
                            <input type="hidden" name="<?= esc('date') ?>" value="<?= date('Y-m-d H:i:s') ?>">
                            <input type="hidden" name="<?= esc('client_id') ?>" value="<?= $client_profile->id ?>">
                            <input type="hidden" name="<?= esc('user_id') ?>" value="<?= basename($_GET['url']) ?>">

                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger text-center col-lg-12">
                                    <?= implode('<br>', $errors);  ?>
                                </div>
                            <?php endif; ?>

                            <input type="text" name="<?= esc('note_title') ?>" class="form-control mb-2" id="note_title" placeholder="Add Note Title">
                            <textarea name="<?= esc('client_notes') ?>" class="form-control mb-3" rows="4" placeholder="Add a new note..."></textarea>
                            <button type="submit" name="<?= esc('submit_note') ?>" class="btn btn-warning text-dark mb-3"><i class="bi bi-plus-lg me-1"></i> Add Note</button>
                        </form>
                        <ul class="list-group">
                            <?php if ($client_notes): foreach ($client_notes as $note): ?>
                                    <li class="list-group-item">
                                        <div class="table-responsive">
                                            <div class="table table-striped table-hover ntoshitable">

                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong><?= $note->note_title . ' - ' . $note->date_created ?></strong> | <small><em>Note by: <?= $note->created_by ?></em></small>
                                                            <p class="mb-0 small text-muted"><?= $note->client_notes ?></p>
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </div>
                                        </div>
                                    </li>
                            <?php endforeach;
                            endif ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Enhanced Modal with Full Schema Fields -->
<div class="modal fade" id="singleDependantViewModal" tabindex="-1" aria-labelledby="beneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header" style="background: linear-gradient(135deg, #cc9933, #5e5205); color: white;">
                <h5 class="modal-title" id="beneficiaryModalLabel">
                    <i class="bi bi-person-badge me-2"></i>Beneficiary Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Avatar & Name -->
                <div class="text-center mb-4">
                    <div class="avatar mx-auto mb-3" style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #cc9933;">
                        <i class="bi bi-person"></i>
                    </div>
                    <h5 class="fw-bold" id="modal-name"></h5>
                    <p class="text-muted mb-0"><span id="modal-relationship"></span> of <span id="modal-member"></span></p>
                </div>

                <!-- Personal Info -->
                <h6 class="border-bottom pb-1 mb-3" style="color: #cc9933;">
                    <i class="bi bi-person-lines-fill me-2"></i>Personal Information
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <strong>ID Number:</strong> <span id="modal-id-number" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Date of Birth:</strong> <span id="modal-dob" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Gender:</strong> <span id="modal-gender" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Marital Status:</strong> <span id="modal-marital-status" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Nationality:</strong> <span id="modal-nationality" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Birth Country:</strong> <span id="modal-birth-country" class="text-muted"></span>
                    </div>
                </div>

                <!-- Policy Details -->
                <h6 class="border-bottom pb-1 mb-3" style="color: #cc9933;">
                    <i class="bi bi-telephone-outbound me-2"></i>Policy Details
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <strong>Policy Number:</strong> <span id="modal-pol-number" class="text-muted"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Effective Date (Force):</strong> <span id="modal-eff-date" class="text-muted"></span>
                    </div>
                    <div class="col-md-12">
                        <strong>Address:</strong> <span id="modal-address" class="text-muted"></span>
                    </div>
                </div>

                <!-- Beneficiary Info -->
                <h6 class="border-bottom pb-1 mb-3" style="color: #cc9933;">
                    <i class="bi bi-award me-2"></i>Beneficiary Status
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <strong>Is Beneficiary:</strong>
                        <span id="modal-is-beneficiary" class="badge" style="background: #cc9933; color: white;"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Share Percentage:</strong> <span id="modal-beneficiary-percentage" class="text-muted"></span>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <strong>Status:</strong> <span id="modal-status" class="badge bg-success"></span>
                </div>

                <!-- Metadata -->
                <h6 class="border-bottom pb-1 mb-3" style="color: #cc9933;">
                    <i class="bi bi-clock-history me-2"></i>Registration & Activity
                </h6>
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Submitted By</dt>
                    <dd class="col-sm-8" id="modal-created-by"></dd>

                    <dt class="col-sm-4 text-muted">Date Created</dt>
                    <dd class="col-sm-8" id="modal-date-created"></dd>

                    <dt class="col-sm-4 text-muted">Updated By</dt>
                    <dd class="col-sm-8" id="modal-updated-by"></dd>

                    <dt class="col-sm-4 text-muted">Last Updated</dt>
                    <dd class="col-sm-8" id="modal-date-updated"></dd>

                    <dt class="col-sm-4 text-muted">Notes</dt>
                    <dd class="col-sm-8">
                        <em id="modal-notes" class="text-muted"></em>
                    </dd>
                </dl>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-warning px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Populator Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.view-dependant-btn');
        const editLink = document.getElementById('modal-edit-link');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                // Text fields
                document.getElementById('modal-name').textContent = this.getAttribute('data-name');
                document.getElementById('modal-member').textContent = this.getAttribute('data-member');
                document.getElementById('modal-relationship').textContent = this.getAttribute('data-relationship');
                document.getElementById('modal-id-number').textContent = this.getAttribute('data-id-number');
                document.getElementById('modal-marital-status').textContent = this.getAttribute('data-marital-status');
                document.getElementById('modal-dob').textContent = this.getAttribute('data-dob');
                document.getElementById('modal-gender').textContent = this.getAttribute('data-gender');
                document.getElementById('modal-nationality').textContent = this.getAttribute('data-nationality');
                document.getElementById('modal-birth-country').textContent = this.getAttribute('data-birth-country');
                document.getElementById('modal-pol-number').textContent = this.getAttribute('data-pol-number');
                document.getElementById('modal-eff-date').textContent = this.getAttribute('data-eff-date');
                document.getElementById('modal-email').textContent = this.getAttribute('data-email');
                document.getElementById('modal-address').textContent = this.getAttribute('data-address');
                document.getElementById('modal-is-beneficiary').textContent = this.getAttribute('data-is-beneficiary');
                document.getElementById('modal-beneficiary-percentage').textContent = this.getAttribute('data-beneficiary-percentage');
                document.getElementById('modal-status').textContent = this.getAttribute('data-status');
                document.getElementById('modal-created-by').textContent = this.getAttribute('data-created-by');
                document.getElementById('modal-date-created').textContent = this.getAttribute('data-date-created');
                document.getElementById('modal-updated-by').textContent = this.getAttribute('data-updated-by');
                document.getElementById('modal-date-updated').textContent = this.getAttribute('data-date-updated');
                document.getElementById('modal-notes').textContent = this.getAttribute('data-notes');

                // Edit link
                const dependantId = this.getAttribute('data-id');
                editLink.href = `<?= ROOT ?>/admin/dependant/edit/${dependantId}`;
            });
        });
    });
</script>

<?php $this->view('inc/footer', $data); ?>