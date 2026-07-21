<?php
/**
 * @var array $data
 * @var string $page_title
 * @var object $comp_detail
 */
$this->view('inc/header', $data)
?>

<main id="main">
    <!-- ======= Breadcrumbs ======= -->
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <h2><?= $page_title ?><a class="btn btn-outline-warning float-end" href="<?= ROOT ?>"><i class="bi bi-arrow-left"></i> BACK HOME</a></h2>

        </div>
    </section><!-- End Breadcrumbs -->
<hr>
    <!-- ======= Privacy Policy Section ======= -->
    <section id="privacy" class="privacy">
        <div class="container" data-aos="fade-up">
            <div class="row">
                <h1>Privacy Policy - <?= $comp_detail->name ?></h1>

                <p><?= $comp_detail->name ?> is committed to protecting the privacy and security of personal information collected from individuals who visit our website or use our services. This Privacy Policy outlines how we collect, use, and disclose personal information and the rights and choices available to individuals regarding their information.</p>

                <h2>1. Information Collection and Use</h2>
                <p><?= $comp_detail->name ?> collects personal information strictly for the purposes of its primary services, including but not limited to information related to members, clients, employees, contractors, and suppliers. Personal information may be collected through our website, over the phone, or in person.</p>

                <h2>2. Information Sharing and Disclosure</h2>
                <p><?= $comp_detail->name ?> will not disclose personal information to third parties without the consent of the individual concerned, unless required or permitted by law. We may share personal information with service providers who assist us in providing our services, subject to appropriate confidentiality and security measures.</p>

                <h2>3. Information Security</h2>
                <p><?= $comp_detail->name ?> implements appropriate technical and organizational measures to safeguard personal information against loss, theft, unauthorized access, disclosure, alteration, or destruction.</p>

                <h2>4. Data Subject Rights</h2>
                <p>Individuals have the right to access, correct, or delete personal information held by <?= $comp_detail->name ?>, as well as the right to object to the processing of personal information in certain circumstances. To exercise these rights, individuals may contact <?= $comp_detail->name ?> using the contact information provided below.</p>

                <h2>5. Cookies</h2>
                <p><?= $comp_detail->name ?> may use cookies and similar tracking technologies to enhance user experience and analyze website usage. Individuals can control cookies through their browser settings, but disabling cookies may affect the functionality of the website.</p>

                <h2>6. Changes to this Privacy Policy</h2>
                <p><?= $comp_detail->name ?> reserves the right to update or modify this Privacy Policy at any time. Any changes will be effective immediately upon posting the revised Privacy Policy on our website.</p>

                <h2>7. Contact Information</h2>
                <p>For inquiries or concerns regarding this Privacy Policy or <?= $comp_detail->name ?>'s handling of personal information, please contact us using the following information:</p>
                <ul>
                    <li>International & Local Phone: <?= $comp_detail->phone ?></li>
                    <li>Email: <a href="mailto:<?= $comp_detail->email ?>"><?= $comp_detail->email ?></a></li>
                </ul>
            </div>
        </div>
    </section><!-- End Privacy Policy Section -->
</main><!-- End #main -->

<?php $this->view('inc/footer', $data) ?>