<?php
    /**
     * @var array $data
     */
    $this->view('inc/header', $data); ?>

<!-- Meeting Header -->
<div class="p-3 mt-2 mx-4 shadow-sm rounded animated-card text-center bg-secondary rounded text-light" style="--animation-order: 1.5;">
    <h3 class="text-dark"><?= esc($data['meeting_title']) ?></h3>
    <?php if (isset($data['meeting_id']) && $data['meeting_id'] !== 'default_room'): ?>
        <p class="text-muted">Meeting ID: <?= esc($data['meeting_id']) ?></p>
    <?php endif; ?>
    <!-- Meeting Status Indicator -->
    <span class="badge bg-<?= $data['meeting_status'] === 'live' ? 'danger' : 'warning' ?>">
        <?= $data['meeting_status'] === 'live' ? 'LIVE NOW' : 'UPCOMING' ?>
    </span>
</div>


<!-- Jitsi Container -->
<!-- <div class="p-3 mt-2 mx-4 bg-body-tertiary shadow-sm rounded animated-card d-flex flex-column align-items-center" style="--animation-order: 2;">
    <div id="jitsi-meet-container" style="width:100%; height:100vh;"></div>
</div>

<script src='https://meet.jit.si/external_api.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const domain = 'meet.jit.si';
        const meetingId = "<?= $data['meeting_id'] ?>";

        const options = {
            roomName: meetingId,
            width: '100%',
            height: '100%',
            parentNode: document.querySelector('#jitsi-meet-container'),
            configOverwrite: {
                startWithAudioMuted: true,
                startWithVideoMuted: false,
                subject: "<?= esc($data['meeting_title']) ?>"
            },
            interfaceConfigOverwrite: {
                MOBILE_APP_PROMO: false,
                SHOW_CHROME_EXTENSION_BANNER: false
            }
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        // Add meeting info to interface
        api.executeCommand('subject', "<?= esc($data['meeting_title']) ?>");
        // Auto-redirect to meeting
        api.on('readyToClose', () => {
            window.location.href = '<?= ROOT ?>/admin/meetings';
        });
    });
</script> -->

<!--Jitsi Direct Container-->
<div class="row">
    <div class="col-lg-12 text-center mt-3">
        <a class="btn btn-success" href="https://meet.jit.si/moderated/9f290b408cf4f5f97509879a339e9e61adb76f049a3aaf6b76a7180aa3f7a9b0?jwt=eyJraWQiOiJqaXRzaS1tb2RlcmF0ZWQtcHJvZC0yMDIxLTA2LTA0IiwiYWxnIjoiUlMyNTYiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJqaXRzaSIsInN1YiI6Im1vZGVyYXRlZCIsImF1ZCI6ImppdHNpIiwicm9vbSI6IjlmMjkwYjQwOGNmNGY1Zjk3NTA5ODc5YTMzOWU5ZTYxYWRiNzZmMDQ5YTNhYWY2Yjc2YTcxODBhYTNmN2E5YjAiLCJ1c2VyX2lkIjoiU2o5ZExZcEhxVk15dlUzVHBRdmZ5UVl0dU44MyIsIm5hbWUiOiJUaGUgVGVjaCBLYWZmaXIiLCJlbWFpbCI6Im1ib2RsYWpvbmd1eG9sb0BnbWFpbC5jb20iLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSzk3dlJTa0dkZ3duVnlBUGNPQ1FJejBlR05neXFrbkJjNDdUcDh2YTNuTFV1NDdKZ0s9czk2LWMifQ.i-RLWG9MbPxfYcd4PVwbQFmT9M32lDJA875CMUCc7Wf-Wr3889aBm7K5fgaGO-n8O7xpaod_9vtwwstma36kBhRQ-auBOe1Znq3afzyFOi6sCCOpYZckS19IiUCRly62dM4YaKOQi56zZPhYlUNOfMyftidA0E4kheMZYgVI7VFytbDi8GOHZJ9iXMDYumz5UZhl31ivfozm2YSx8_QYliEwuAunPV60GR1MeOYQ1L4KDpPjS9Ee9ssgkpW40NcrrMpaeJmRMBV4yC4bZ07HW-nB9jK6v-a6Z0c8yjHM0-2mtXpFB1s2hca6a-3hf7ZmB3hn42_O4rBMg7Um9TlkiQ">START/JOIN MEETING</a>
    </div>
</div>
  
<?php $this->view('inc/footer') ?>