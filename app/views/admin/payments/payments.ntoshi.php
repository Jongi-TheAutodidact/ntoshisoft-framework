<?php
/** @var array $payments */
/** @var float|string|null $sum_eft_pay */
/** @var float|string|null $sum_cash_pay */
/** @var array $data */
$this->view('inc/header', $data); ?>

<main id="main" class="main">

    <section class="section p-4">

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (user('user_role') == 'Admin' || user('user_role') == 'Manager') : ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="fs-4 page-title">Payments List</h3>
                                <a href="<?= ROOT . '/admin/payment/create' ?>" class="btn btn-warning text-dark"><i class="bi bi-plus-circle me-2"></i>Add New Payment</a>
                            </div>
                            <hr>
                        <?php endif; ?>

                        <?= Util::displayFlash('payment_register_success', 'success') ?>
                        <?= Util::displayFlash('payment_update_success', 'success') ?>
                        <?= Util::displayFlash('payment_delete_success', 'success') ?>

                        <div class="row">
                            <!-- Table with stripped rows -->
                            <div class="table-responsive">
                                <table class="table ntoshitable table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Date</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Payment Type</th>
                                            <th>Paid Via</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1;
                                        if (!empty($payments)) :
                                            foreach ($payments as $payment) :
                                        ?>
                                                <tr>
                                                    <td><?= $counter++ ?></td>
                                                    <td><?= date("F j, Y", strtotime($payment->payment_date)) ?></td>
                                                    <td><?= esc($payment->firstname . ' ' . $payment->surname ?? 'N/A') ?></td>
                                                    <td>R <?= number_format((float)$payment->amount, 2) ?></td>
                                                    <td>
                                                        <?php  
                                                            $badgeClass = match($payment->pay_type) {
                                                                'Other' => 'bg-warning text-dark',
                                                                'Debt' => 'bg-success text-dark',
                                                                default => 'bg-primary text-white'
                                                            };
                                                        ?>
                                                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($payment->pay_type) ?></span>
                                                    </td>
                                                    <td>
                                                        <?php  
                                                            $badgeClass = match($payment->paid_via) {
                                                                'Cash' => 'bg-warning text-dark',
                                                                'EFT' => 'bg-success text-dark',
                                                                default => 'bg-primary text-white'
                                                            };
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($payment->paid_via) ?></span>
                                                    </td>


                                                    <td>
                                                        <div class="text-center d-flex gap-2 justify-content-center">
                                                            <button type="button" class="btn btn-warning text-dark view-product-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#singlePaymentViewModal"
                                                                data-id="<?= $payment->id ?>"
                                                                data-payment-date="<?= esc($payment->payment_date ?? 'N/A') ?>"
                                                                data-client-name="<?= esc($payment->client_name ?? 'N/A') ?>"
                                                                data-payment-amount="<?= esc($payment->amount ?? 'N/A') ?>"
                                                                data-payment-pay_type="<?= esc($payment->pay_type ?? 'N/A') ?>"
                                                                data-paid-via="<?= esc($payment->paid_via ?? 'N/A') ?>"
                                                                data-captured="<?= esc($payment->captured ?? 'N/A') ?>"
                                                                data-notes="<?= esc($payment->notes ?? 'N/A') ?>"
                                                                data-created-by="<?= esc($payment->created_by) ?>"
                                                                data-date-created="<?= esc($payment->date_created) ?>"
                                                                data-date-updated="<?= esc($payment->date_updated) ?>"
                                                                data-created-by="<?= esc($payment->created_by) ?>">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <a href="<?= ROOT ?>/admin/payment/edit/<?= $payment->id ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                                            <a href="<?= ROOT ?>/admin/payment/delete/<?= $payment->id ?>"
                                                                onclick="return confirm('Are you sure you want to delete this payment? This action cannot be reversed.')"
                                                                class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- Modal -->
                                                <div class="modal fade" id="singlePaymentViewModal" tabindex="-1" aria-labelledby="singlePPViewModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="singlePPViewModalLabel">Product Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-4 text-center">
                                                                        <p><strong><i class="bi bi-person fs-1 primary-text border rounded-full secondary-bg p-3"></i> <br><br> Client:</strong> <span id="modal-client-name"></span></p>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <p><strong>1. Payment Date:</strong> <span id="modal-payment-date"></span></p>
                                                                        <p><strong>2. Payment Amount:</strong> <span id="modal-payment-amount"></span></p>
                                                                        <p><strong>3. Payment Type:</strong> <?php
                                                                                                                switch ($payment->pay_type) {
                                                                                                                    case 'Seeding': ?>
                                                                                    <span class="badge bg-success"> <?= $payment->pay_type ?></span>
                                                                                <?php break;
                                                                                                                    case 'Membership Fee': ?>
                                                                                    <span class="badge bg-danger"><?= $payment->pay_type ?></span>
                                                                                <?php break;

                                                                                                                    default: ?>
                                                                                    <span class="badge bg-warning text-light"><?= $payment->pay_type ?></span>
                                                                            <?php
                                                                                                                        break;
                                                                                                                }
                                                                            ?>
                                                                        </p>
                                                                        <p><strong>4. Paid Via:</strong> <span id="modal-paid-via"></span></p>
                                                                        <p><strong>5. Captured on system?:</strong> <span id="modal-captured"></span></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <h3>Notes</h3>
                                                                        <p id="modal-notes"></p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <p><strong>Payment received by:</strong> <span id="modal-created-by"></span> on <span id="modal-date-created"> and updated by(if applicable): <span id="modal-updated-by"></span></span></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            endforeach;
                                        else : ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No payments found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <!-- Table Footer with Totals -->
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">EFT:</td>
                                            <td>R<?= number_format((float)($sum_eft_pay ?? 0), 2) ?></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Cash:</td>
                                            <td>R<?= number_format((float)($sum_cash_pay ?? 0), 2) ?></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td colspan="3" class="text-end fw-bold fs-5">TOTAL PAYMENTS:</td>
                                            <td class="fw-bold fs-5">
                                                <span class="bg-success px-2 py-1 rounded text-white">
                                                    R<?= number_format((float)(($sum_eft_pay ?? 0) + ($sum_cash_pay ?? 0)), 2) ?>
                                                </span>
                                            </td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- End Table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-product-btn');

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const paymentDate = this.getAttribute('data-payment-date');
                const clientName = this.getAttribute('data-client-name');
                const paymentAmount = this.getAttribute('data-payment-amount');
                const payType = this.getAttribute('data-payment-pay_type');
                const paidVia = this.getAttribute('data-paid-via');
                const capturedYesNo = this.getAttribute('data-captured');
                const dateCreated = this.getAttribute('data-date-created');
                const notes = this.getAttribute('data-notes');
                const createdBy = this.getAttribute('data-created-by');

                // Map 1/0 to Yes/No
                function yesNo(value) {
                    return value === '1' ? 'Yes' : (value === '0' ? 'No' : 'N/A');
                }

                document.getElementById('modal-payment-date').textContent = paymentDate;
                document.getElementById('modal-client-name').textContent = clientName;
                document.getElementById('modal-payment-amount').textContent = paymentAmount;
                document.getElementById('modal-payment-pay_type').textContent = yesNo(payType);
                document.getElementById('modal-paid-via').textContent = paidVia;
                document.getElementById('modal-captured').textContent = yesNo(capturedYesNo);
                document.getElementById('modal-date-created').textContent = dateCreated;
                document.getElementById('modal-notes').textContent = notes;
                document.getElementById('modal-created-by').textContent = createdBy;
            });
        });
    });
</script>
<!-- ======= Footer ======= -->
<?php $this->view('inc/footer') ?>