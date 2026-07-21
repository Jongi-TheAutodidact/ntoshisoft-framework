<?php
    /**
     * @var string $page_title
     * @var array $data
     */
    $this->view('inc/header', $data);
?>

<!-- Page Header -->
<div class="ns-page-header my-4">
    <div>
        <h1 class="ns-page-title">
            <i class="bi bi-music-note me-2"></i>
            <?= htmlspecialchars($page_title) ?>
        </h1>
        <p class="ns-page-subtitle">Feed and Heal Yours and The Young One's Soul</p>
    </div>
    <div class="ns-actions">
        <div class="ns-dropdown">
            <a href="<?= ROOT . '/admin' ?>" class="ns-btn ns-btn-secondary">
                <i class="bi bi-x-circle"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- ============================================
     AUDIO PLAYER SECTION - Multimedia
     ============================================ -->
<section class="ns-section" id="audio-demo">
    <div class="container">
        <div class="ns-section-header ns-text-center ns-mb-xl">
            <h2 class="ns-section-title display-heading">Multimedia Player</h2>
            <p class="ns-section-subtitle">Audio player - Music Feeds The Young Ones' Souls</p>
        </div>

        <div class="ns-card">
            <div class="ns-card-body">
                <!-- Playlist -->
                <ul id="cmt-playlist" class="ns-audio-playlist">
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Tribute - Indumiso Educare Centre.mp3' ?>">
                        <span class="ns-audio-number">01</span>
                        <span class="ns-audio-title">Tribute - Indumiso Educare Centre</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Indumiso Educare Centre - Grad Song 2024.mp3' ?>">
                        <span class="ns-audio-number">02</span>
                        <span class="ns-audio-title">Indumiso Educare Centre - Grad Song 2024</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Noma Singahlupheka.mp3' ?>">
                        <span class="ns-audio-number">03</span>
                        <span class="ns-audio-title">Noma Singahlupheka</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . "/assets/audio/Owaqal'esemncinane.mp3" ?>">
                        <span class="ns-audio-number">04</span>
                        <span class="ns-audio-title">Owaqal'esemncinane Ukumkhonz' uThixo Wakhe</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Bawo ndingumntwana waKho.mp3' ?>">
                        <span class="ns-audio-number">05</span>
                        <span class="ns-audio-title">Bawo ndingumntwana waKho</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Jesu Esiphambanweni.mp3' ?>">
                        <span class="ns-audio-number">06</span>
                        <span class="ns-audio-title">Jesu Esiphambanweni</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Wenjenj\'uThixo Ukulithanda Kwakh\'ihlabathi.mp3' ?>">
                        <span class="ns-audio-number">07</span>
                        <span class="ns-audio-title">Wenjenj'uThixo Ukulithanda Kwakh'ihlabathi</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                    <li class="ns-audio-item" data-src="<?= ROOT . '/assets/audio/Zingandidakumbisa na ezintlungu.mp3' ?>">
                        <span class="ns-audio-number">08</span>
                        <span class="ns-audio-title">Zingandidakumbisa na ezintlungu</span>
                        <i class="bi bi-music-note-list ns-audio-icon"></i>
                    </li>
                </ul>

                <audio id="cmt-audio" preload="auto"></audio>

                <!-- Audio Controls -->
                <div class="ns-audio-controls">
                    <button id="cmt-prev" class="ns-btn ns-btn-ghost ns-btn-sm">
                        <i class="bi bi-skip-backward-fill"></i>
                    </button>
                    <button id="cmt-play" class="ns-btn ns-btn-primary ns-btn-lg">
                        <i class="bi bi-play-fill"></i>
                    </button>
                    <button id="cmt-pause" class="ns-btn ns-btn-primary ns-btn-lg d-none">
                        <i class="bi bi-pause-fill"></i>
                    </button>
                    <button id="cmt-stop" class="ns-btn ns-btn-ghost ns-btn-sm">
                        <i class="bi bi-stop-fill"></i>
                    </button>
                    <button id="cmt-next" class="ns-btn ns-btn-ghost ns-btn-sm">
                        <i class="bi bi-skip-forward-fill"></i>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="ns-audio-progress">
                    <div class="ns-progress">
                        <div id="cmt-progress" class="ns-progress-bar" style="width: 0%;"></div>
                    </div>
                    <div class="ns-audio-time">
                        <span id="cmt-current">0:00</span>
                        <span id="cmt-duration">0:00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
  /* Audio Section Styles */
  .ns-audio-playlist {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
  }

  .ns-audio-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .ns-audio-item:hover,
  .ns-audio-item.active {
    background: rgba(0, 240, 255, 0.1);
    border-color: rgba(0, 240, 255, 0.3);
  }

  .ns-audio-item.active {
    box-shadow: 0 0 15px rgba(0, 240, 255, 0.3);
  }

  .ns-audio-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    color: var(--ns-accent);
    min-width: 30px;
  }

  .ns-audio-title {
    flex: 1;
    font-family: 'Rajdhani', sans-serif;
    font-weight: 600;
    color: var(--ns-text-primary);
  }

  .ns-audio-icon {
    color: var(--ns-text-muted);
  }

  .ns-audio-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 1.5rem 0;
  }

  .ns-audio-progress {
    margin-top: 1rem;
  }

  .ns-audio-time {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: var(--ns-text-muted);
    margin-top: 0.5rem;
  }
</style>


</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Audio Player Script - In here-->
<script src="<?= ROOT . '/assets/js/landing.js' ?>"></script>