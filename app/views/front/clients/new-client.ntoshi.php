<?php
/** @var array $data */
/** @var string $page_title */
$this->view('inc/front-header', $data) ?>

<main id="main">
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <h2 class="text-light"><?= $page_title ?><a class="btn btn-outline-<?= THEME_COLOR ?> float-end" href="<?= ROOT ?>"><i class="bi bi-arrow-left"></i> BACK HOME</a></h2>
        </div>
    </section><!-- End Breadcrumbs -->

    <section class="container">
        <div class="row gx-4 gx-lg-5">
            <div class="col-lg-12 mb-5">
                <div class="frontend-card h-100 p-4">
                    <h4 class="card-title mb-3 text-center">Important: Membership Payment Instructions</h4>
                    <p class="mb-3">
                        Thank you for your interest in becoming a member of <strong>Masincedane Mart Forum</strong>.
                        Before completing the membership application form, please note that an annual membership fee of <span class="bg-success px-2 py-1 rounded">R500</span> is required.
                    </p>
                    <p class="mb-3">
                        Our official banking details will be made available soon. In the meantime, you may arrange an alternative payment method with one of our team members:
                    </p>
                    <ul class="mb-3">
                        <li>Use a temporary account provided upon request.</li>
                        <li>Make a <strong>"Send Cash"</strong> payment (e.g. via eWallet, CashSend, etc.).</li>
                    </ul>
                    <p class="mb-3">
                        If using a cash voucher service, please
                        <a href="https://wa.me/27783819701?text=Wish%20to%20Send%20Voucher%20Number%20For%20New%20Membership%20Application.%20Please%20confirm%20your%20availability" target="_blank">
                            submit only the <strong>voucher number</strong> via WhatsApp.
                        </a>
                        <span class="text-danger"> Never send the PIN via the form.</span>
                        A team member will contact you via a secure phone call to collect the PIN personally, ensuring safety and verification.
                    </p>
                    <div class="alert alert-warning mt-4" role="alert">
                        <strong>Security Reminder:</strong> Do not pair the voucher number and PIN in any written or digital communication.
                    </div>
                    <p class="mt-3">
                        Once payment is arranged, you may proceed to complete your membership <strong><u><em>via a link that will be shared with you via WhatsApp</em></u></strong>.
                    </p>
                </div>
            </div>
            <hr>
            <div class="col-lg-12 text-center my-5">
                <a class="btn btn-success rounded" href="<?= ROOT . '/new-policy-user' ?>">STEP 1 - Register As A User</a>
            </div>
            <hr>
        </div>

    </section>
</main><!-- End #main -->

<?php $this->view('inc/front-footer', $data) ?>