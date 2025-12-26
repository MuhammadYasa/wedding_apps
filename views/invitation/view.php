<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Rsvp;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guest app\models\Guest|null */
/* @var $galleries app\models\Gallery[] */
/* @var $recentRsvps app\models\Rsvp[] */
/* @var $existingRsvp app\models\Rsvp|null */
/* @var $guestNameFromUrl string|null */

$this->title = $invitation->title;

// SEO Meta Tags
$this->registerMetaTag(['name' => 'description', 'content' => "Undangan pernikahan {$invitation->bride_name} & {$invitation->groom_name} - {$invitation->title}. " . date('d F Y', $invitation->event_date) . " di {$invitation->venue}"]);
$this->registerMetaTag(['name' => 'keywords', 'content' => "undangan pernikahan, {$invitation->bride_name}, {$invitation->groom_name}, wedding invitation, " . date('Y', $invitation->event_date)]);

// Open Graph Meta Tags for Social Sharing
$invitationUrl = Url::to(['invitation/view', 'slug' => $invitation->slug], true);
$ogImage = !empty($invitation->galleries) 
    ? Url::to('@web/uploads/galleries/' . $invitation->galleries[0]->filename, true)
    : Url::to('@web/images/default-wedding.jpg', true);

$this->registerMetaTag(['property' => 'og:title', 'content' => $invitation->title]);
$this->registerMetaTag(['property' => 'og:description', 'content' => "Undangan pernikahan {$invitation->bride_name} & {$invitation->groom_name} - " . date('d F Y', $invitation->event_date)]);
$this->registerMetaTag(['property' => 'og:image', 'content' => $ogImage]);
$this->registerMetaTag(['property' => 'og:url', 'content' => $invitationUrl]);
$this->registerMetaTag(['property' => 'og:type', 'content' => 'website']);
$this->registerMetaTag(['property' => 'og:site_name', 'content' => 'Wedding Invitation']);

// Twitter Card Meta Tags
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $invitation->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => "Undangan pernikahan {$invitation->bride_name} & {$invitation->groom_name}"]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $ogImage]);

// Register base CSS
$this->registerCssFile('@web/css/invitation.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);

// Register theme CSS based on invitation theme
$theme = $invitation->theme ?? 'default';
$this->registerCssFile("@web/css/themes/{$theme}.css", ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);

// Register Lightbox CSS & JS
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Register countdown JS
$this->registerJsFile('@web/js/countdown.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Pass event date to JavaScript
$eventTimestamp = $invitation->event_date * 1000; // Convert to milliseconds for JS
$this->registerJs("
    // Initialize countdown
    initCountdown($eventTimestamp);
    
    // Smooth scroll
    document.querySelectorAll('a[href^=\"#\"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
", \yii\web\View::POS_READY);
?>

<!-- Hero Section -->
<section class="hero-section" id="home" style="background-image: url('<?= $invitation->getCoverImageUrl() ?>');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container text-center">
            <?php if ($guest): ?>
                <p class="guest-greeting animate-fade-in">Kepada Yth.</p>
                <h3 class="guest-name animate-fade-in"><?= Html::encode($guest->name) ?></h3>
                <p class="guest-message animate-fade-in">Di Tempat</p>
                <hr class="divider">
            <?php endif; ?>
            
            <h1 class="couple-names animate-fade-in-up">
                <?= Html::encode($invitation->bride_name) ?>
                <span class="ampersand">&</span>
                <?= Html::encode($invitation->groom_name) ?>
            </h1>
            
            <p class="wedding-date animate-fade-in-up">
                <i class="bi bi-calendar-heart"></i>
                <?= date('d F Y', $invitation->event_date) ?>
            </p>
            
            <div class="hero-actions animate-fade-in-up">
                <a href="#rsvp" class="btn btn-primary btn-lg">
                    <i class="bi bi-envelope-heart"></i> Konfirmasi Kehadiran
                </a>
                <a href="<?= Url::to(['print', 'slug' => $invitation->slug]) ?>" class="btn btn-outline-light btn-lg" target="_blank">
                    <i class="bi bi-printer"></i> Versi Cetak
                </a>
            </div>
        </div>
    </div>
    <div class="scroll-indicator">
        <a href="#couple">
            <i class="bi bi-chevron-down"></i>
        </a>
    </div>
</section>

<!-- Countdown Section -->
<section class="countdown-section" id="countdown">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="countdown-box">
                    <h3 class="text-center mb-4">Menghitung Hari</h3>
                    <div class="countdown-timer" id="countdown-timer">
                        <div class="countdown-item">
                            <span class="countdown-value" id="days">0</span>
                            <span class="countdown-label">Hari</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-value" id="hours">0</span>
                            <span class="countdown-label">Jam</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-value" id="minutes">0</span>
                            <span class="countdown-label">Menit</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-value" id="seconds">0</span>
                            <span class="countdown-label">Detik</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Couple Section -->
<section class="couple-section" id="couple">
    <div class="container">
        <div class="section-header text-center">
            <h2>Assalamu'alaikum Warahmatullahi Wabarakatuh</h2>
            <p class="lead">
                Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan acara pernikahan putra-putri kami:
            </p>
        </div>

        <div class="row align-items-center mt-5">
            <div class="col-md-5 text-center">
                <div class="couple-card">
                    <div class="couple-icon">
                        <i class="bi bi-person-hearts"></i>
                    </div>
                    <h3><?= Html::encode($invitation->bride_name) ?></h3>
                    <p class="couple-parents">
                        Putri dari:<br>
                        <?php if ($invitation->bride_father): ?>
                            <?= Html::encode($invitation->bride_father) ?><br>
                        <?php endif; ?>
                        <?php if ($invitation->bride_mother): ?>
                            <?= Html::encode($invitation->bride_mother) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <div class="col-md-2 text-center">
                <div class="couple-divider">
                    <i class="bi bi-heart-fill"></i>
                </div>
            </div>
            
            <div class="col-md-5 text-center">
                <div class="couple-card">
                    <div class="couple-icon">
                        <i class="bi bi-person-hearts"></i>
                    </div>
                    <h3><?= Html::encode($invitation->groom_name) ?></h3>
                    <p class="couple-parents">
                        Putra dari:<br>
                        <?php if ($invitation->groom_father): ?>
                            <?= Html::encode($invitation->groom_father) ?><br>
                        <?php endif; ?>
                        <?php if ($invitation->groom_mother): ?>
                            <?= Html::encode($invitation->groom_mother) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($invitation->story): ?>
<!-- Story Section -->
<section class="story-section" id="story">
    <div class="container">
        <div class="section-header text-center">
            <h2>Cerita Kami</h2>
            <div class="divider-heart">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="story-content">
                    <?= nl2br(Html::encode($invitation->story)) ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Event Details Section -->
<section class="event-section" id="event">
    <div class="container">
        <div class="section-header text-center">
            <h2>Detail Acara</h2>
            <div class="divider-heart">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
        
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="event-card">
                    <div class="event-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <h3><?= Html::encode($invitation->title) ?></h3>
                    <div class="event-details">
                        <p>
                            <i class="bi bi-calendar3"></i>
                            <strong><?= date('l, d F Y', $invitation->event_date) ?></strong>
                        </p>
                        <?php if ($invitation->event_time): ?>
                        <p>
                            <i class="bi bi-clock"></i>
                            <?= Html::encode($invitation->event_time) ?>
                        </p>
                        <?php endif; ?>
                        <p>
                            <i class="bi bi-geo-alt"></i>
                            <?= Html::encode($invitation->venue) ?>
                        </p>
                        <?php if ($invitation->venue_address): ?>
                        <p class="text-muted small">
                            <?= Html::encode($invitation->venue_address) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($invitation->venue_map_url): ?>
                    <div class="event-actions mt-4">
                        <a href="<?= Html::encode($invitation->venue_map_url) ?>" 
                           target="_blank" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-map"></i> Buka Google Maps
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<?php if ($invitation->venue_lat && $invitation->venue_lng): ?>
<section class="map-section" id="map">
    <div class="container-fluid p-0">
        <div class="map-container">
            <iframe 
                src="https://maps.google.com/maps?q=<?= $invitation->venue_lat ?>,<?= $invitation->venue_lng ?>&z=15&output=embed"
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if (!empty($galleries)): ?>
<section class="gallery-section" id="gallery">
    <div class="container">
        <div class="section-header text-center">
            <h2>Galeri Foto</h2>
            <div class="divider-heart">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
        
        <div class="row g-3 mt-4">
            <?php foreach ($galleries as $gallery): ?>
            <div class="col-md-4 col-sm-6">
                <a href="<?= $gallery->getImageUrl() ?>" 
                   data-lightbox="gallery" 
                   data-title="<?= Html::encode($gallery->caption ?? '') ?>"
                   class="gallery-link">
                    <div class="gallery-item">
                        <img src="<?= $gallery->getImageUrl() ?>" 
                             alt="<?= Html::encode($gallery->caption ?? '') ?>" 
                             class="img-fluid">
                        <?php if ($gallery->caption): ?>
                        <div class="gallery-caption">
                            <i class="bi bi-zoom-in"></i>
                            <?= Html::encode($gallery->caption) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- RSVP Section -->
<section class="rsvp-section" id="rsvp">
    <div class="container">
        <div class="section-header text-center">
            <h2>Konfirmasi Kehadiran</h2>
            <p class="lead">Mohon untuk mengisi form konfirmasi kehadiran di bawah ini</p>
        </div>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-8 col-lg-6">
                <?php
                // Display flash messages
                if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= Yii::$app->session->getFlash('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= Yii::$app->session->getFlash('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php
                // Jika tidak ada guest (tidak ada undangan), tampilkan pesan
                if (!$guest): ?>
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Akses Terbatas</h5>
                                <p class="mb-0">
                                    Form konfirmasi kehadiran hanya dapat diakses melalui <strong>link undangan personal</strong> yang telah dikirimkan kepada Anda.
                                </p>
                                <hr class="my-2">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Jika Anda belum menerima link undangan, silakan hubungi penyelenggara acara.
                                </small>
                            </div>
                        </div>
                    </div>
                <?php 
                // Jika guest sudah RSVP, tampilkan konfirmasi
                elseif (isset($existingRsvp) && $existingRsvp): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <div class="mb-4">
                                <?php if ($existingRsvp->attendance === \app\models\Rsvp::ATTENDANCE_ATTENDING): ?>
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 text-success fw-bold">Terima Kasih Atas Konfirmasi Anda!</h4>
                                    <p class="text-muted mb-0">Anda telah mengkonfirmasi akan <strong class="text-success">HADIR</strong> di acara kami.</p>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3 fw-bold">Terima Kasih Atas Konfirmasi Anda</h4>
                                    <p class="text-muted mb-0">Anda telah mengkonfirmasi <strong class="text-danger">TIDAK BISA HADIR</strong> di acara kami.</p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($existingRsvp->message): ?>
                                <div class="alert alert-light border-0 mb-3">
                                    <small class="text-muted d-block mb-2"><strong>Pesan & Doa Anda:</strong></small>
                                    <p class="mb-0 fst-italic text-dark">"<?= Html::encode($existingRsvp->message) ?>"</p>
                                </div>
                            <?php endif; ?>
                            
                            <hr class="my-4">
                            <p class="text-muted small mb-0">
                                <i class="bi bi-clock me-1"></i>
                                Dikonfirmasi pada <strong><?= date('d F Y, H:i', $existingRsvp->created_at) ?></strong> WIB
                                <br>
                                <i class="bi bi-info-circle me-1"></i>
                                Konfirmasi kehadiran hanya dapat dilakukan satu kali.
                            </p>
                        </div>
                    </div>
                <?php 
                // Jika guest belum RSVP, tampilkan form
                else:
                    $rsvpModel = new Rsvp();
                    $rsvpModel->invitation_id = $invitation->id;
                
                $form = ActiveForm::begin([
                    'id' => 'rsvp-form',
                    'action' => ['invitation/rsvp', 'slug' => $invitation->slug],
                    'options' => ['class' => 'rsvp-form-container'],
                    'enableClientValidation' => true,
                    'enableAjaxValidation' => false,
                ]); 
                ?>
                
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        
                        <?php if ($guestNameFromUrl): ?>
                            <!-- Display guest name (read-only) -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Kepada Yth.</label>
                                <div class="guest-name-display p-3 bg-light rounded text-center">
                                    <h4 class="mb-0 text-primary">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <?= Html::encode($guestNameFromUrl) ?>
                                    </h4>
                                </div>
                                <?php
                                // Generate email from guest name - sanitize special characters
                                $emailPrefix = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $guestNameFromUrl));
                                $emailPrefix = trim($emailPrefix, '.'); // Remove leading/trailing dots
                                $guestEmail = $emailPrefix . '@guest.com';
                                ?>
                                <?= $form->field($rsvpModel, 'name')->hiddenInput(['value' => $guestNameFromUrl])->label(false) ?>
                                <?= $form->field($rsvpModel, 'email')->hiddenInput(['value' => $guestEmail])->label(false) ?>
                            </div>
                        <?php else: ?>
                            <!-- Manual input if no guest name in URL -->
                            <?= $form->field($rsvpModel, 'name')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Masukkan nama lengkap Anda',
                                'class' => 'form-control form-control-lg'
                            ])->label('Nama Lengkap <span class="text-danger">*</span>', ['encode' => false]) ?>

                            <?= $form->field($rsvpModel, 'email')->textInput([
                                'maxlength' => true,
                                'type' => 'email',
                                'placeholder' => 'contoh@email.com',
                                'class' => 'form-control form-control-lg'
                            ])->label('Email <span class="text-danger">*</span>', ['encode' => false]) ?>
                        <?php endif; ?>

                        <div class="form-group mb-4">
                            <?= $form->field($rsvpModel, 'attendance')->radioList(
                                Rsvp::getAttendanceOptions(),
                                [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $icon = $value === 'attending' ? 'check-circle' : 'x-circle';
                                        $color = $value === 'attending' ? 'success' : 'danger';
                                        
                                        return '<div class="form-check attendance-option mb-3">
                                            <input type="radio" id="attendance-' . $value . '" class="form-check-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>
                                            <label class="form-check-label w-100" for="attendance-' . $value . '">
                                                <div class="attendance-card border border-' . $color . ' rounded p-3">
                                                    <i class="bi bi-' . $icon . ' text-' . $color . ' me-2 fs-4"></i>
                                                    <span class="fs-5">' . $label . '</span>
                                                </div>
                                            </label>
                                        </div>';
                                    }
                                ]
                            )->label('Konfirmasi Kehadiran <span class="text-danger">*</span>', ['encode' => false]) ?>
                        </div>

                        <?= $form->field($rsvpModel, 'message')->textarea([
                            'rows' => 4,
                            'placeholder' => 'Tuliskan ucapan dan doa untuk kami...',
                            'class' => 'form-control'
                        ])->label('Pesan & Doa') ?>

                        <div class="d-grid gap-2 mt-4">
                            <?= Html::submitButton(
                                '<i class="bi bi-send me-2"></i>Kirim Konfirmasi',
                                ['class' => 'btn btn-primary btn-lg']
                            ) ?>
                        </div>
                    </div>
                </div>
                
                <?php ActiveForm::end(); ?>
                <?php endif; // end if guest belum RSVP ?>
            </div>
        </div>
    </div>
</section>

<!-- Wishes Section (Recent RSVPs) -->
<?php if (!empty($recentRsvps)): ?>
<section class="wishes-section" id="wishes">
    <div class="container">
        <div class="section-header text-center">
            <h2>Ucapan & Doa</h2>
            <div class="divider-heart">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
        
        <div class="row mt-4">
            <?php foreach (array_slice($recentRsvps, 0, 6) as $rsvp): ?>
                <?php if ($rsvp->message): ?>
                <div class="col-md-6 mb-3">
                    <div class="wish-card">
                        <h5><?= Html::encode($rsvp->name) ?></h5>
                        <p class="wish-message"><?= Html::encode($rsvp->message) ?></p>
                        <small class="text-muted">
                            <?= date('d M Y', $rsvp->created_at) ?>
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Wishes Section -->
<section class="wishes-section" id="wishes">
    <div class="container">
        <div class="section-header text-center">
            <h2>Ucapan & Doa</h2>
            <div class="divider-heart">
                <i class="bi bi-journal-heart-fill"></i>
            </div>
            <p class="text-muted">Tinggalkan ucapan dan doa terbaik untuk kami</p>
        </div>
        
        <div class="text-center mt-4 mb-5">
            <?= Html::a('<i class="bi bi-pen-fill me-2"></i> Tulis Ucapan', 
                ['wish/create', 'id' => $invitation->id], 
                ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
        
        <?php
        $recentWishes = \app\models\Wish::getApprovedWishes($invitation->id, 6);
        if (!empty($recentWishes)):
        ?>
            <div class="row g-4">
                <?php foreach ($recentWishes as $wish): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm wish-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar-circle me-3">
                                        <?= strtoupper(mb_substr($wish->name, 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= Html::encode($wish->name) ?></h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= Yii::$app->formatter->asRelativeTime($wish->created_at) ?>
                                        </small>
                                    </div>
                                </div>
                                <p class="wish-message mb-0">
                                    <i class="bi bi-quote text-muted"></i>
                                    <?= nl2br(Html::encode($wish->getShortMessage(150))) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <?= Html::a('Lihat Semua Ucapan <i class="bi bi-arrow-right ms-2"></i>', 
                    ['wish/index', 'id' => $invitation->id], 
                    ['class' => 'btn btn-outline-primary']) ?>
            </div>
        <?php else: ?>
            <div class="text-center">
                <i class="bi bi-inbox display-3 text-muted"></i>
                <p class="text-muted mt-3">Belum ada ucapan. Jadilah yang pertama!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Live Chat Section -->
<section class="chat-section" id="chat">
    <div class="container">
        <div class="section-header text-center">
            <h2>Live Chat</h2>
            <div class="divider-heart">
                <i class="bi bi-chat-heart-fill"></i>
            </div>
            <p class="text-muted">Ngobrol santai dengan tamu undangan lainnya</p>
        </div>
        
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="chat-container card border-0 shadow-lg">
                    <!-- Chat Messages -->
                    <div class="chat-messages" id="chatMessages">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1"></i>
                            <p class="mt-2">Memuat percakapan...</p>
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="chat-input-container">
                        <?php if ($guest): ?>
                            <form id="chatForm" class="d-flex gap-2">
                                <input type="text" 
                                       id="chatGuestName" 
                                       class="form-control chat-name-readonly" 
                                       value="<?= Html::encode($guestNameFromUrl) ?>"
                                       readonly
                                       tabindex="-1">
                                <input type="text" 
                                       id="chatMessage" 
                                       class="form-control flex-grow-1" 
                                       placeholder="Tulis pesan..."
                                       required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0 text-center">
                                <i class="bi bi-lock-fill me-2"></i>
                                <strong>Live chat hanya tersedia untuk tamu yang memiliki link undangan.</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Register Chat JS -->
<?php
$hasGuest = ($guest !== null);
$this->registerJs("
    const chatSlug = '" . $invitation->slug . "';
    const hasGuest = " . ($hasGuest ? 'true' : 'false') . ";
    let lastMessageId = 0;
    let isScrolledToBottom = true;
    
    // Load messages
    function loadMessages() {
        $.ajax({
            url: '" . Url::to(['invitation/get-messages', 'slug' => $invitation->slug]) . "',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayMessages(response.messages);
                }
            }
        });
    }
    
    // Display messages
    function displayMessages(messages) {
        const container = $('#chatMessages');
        const wasEmpty = container.find('.chat-message').length === 0;
        
        if (wasEmpty) {
            container.empty();
        }
        
        messages.forEach(function(msg) {
            if (msg.id > lastMessageId) {
                const messageHtml = `
                    <div class=\"chat-message\" data-id=\"\${msg.id}\">
                        <div class=\"message-header\">
                            <strong class=\"guest-name\">\${escapeHtml(msg.guest_name)}</strong>
                            <small class=\"text-muted\">\${msg.formatted_time}</small>
                        </div>
                        <div class=\"message-body\">\${escapeHtml(msg.message)}</div>
                    </div>
                `;
                container.append(messageHtml);
                lastMessageId = msg.id;
            }
        });
        
        if (isScrolledToBottom || wasEmpty) {
            scrollToBottom();
        }
    }
    
    // Send message
    $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if user has guest access
        if (!hasGuest) {
            alert('Live chat hanya tersedia untuk tamu yang memiliki link undangan.');
            return false;
        }
        
        const guestName = $('#chatGuestName').val().trim();
        const message = $('#chatMessage').val().trim();
        
        console.log('Sending message:', { guestName, message });
        
        if (!guestName || !message) {
            alert('Nama dan pesan harus diisi');
            return;
        }
        
        const postData = {
            guest_name: guestName,
            message: message,
            " . Yii::$app->request->csrfParam . ": '" . Yii::$app->request->csrfToken . "'
        };
        
        console.log('POST data:', postData);
        
        $.ajax({
            url: '" . Url::to(['invitation/send-message', 'slug' => $invitation->slug]) . "',
            method: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    $('#chatMessage').val('');
                    displayMessages([response.message]);
                } else {
                    let errorMsg = response.error || 'Gagal mengirim pesan';
                    if (response.validation_errors) {
                        console.error('Validation errors:', response.validation_errors);
                        errorMsg += '\\n' + JSON.stringify(response.validation_errors);
                    }
                    alert(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr, status, error});
                console.error('Response text:', xhr.responseText);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
    
    // Scroll to bottom
    function scrollToBottom() {
        const container = $('#chatMessages');
        container.scrollTop(container[0].scrollHeight);
    }
    
    // Check if scrolled to bottom
    $('#chatMessages').on('scroll', function() {
        const elem = $(this)[0];
        isScrolledToBottom = elem.scrollHeight - elem.scrollTop <= elem.clientHeight + 50;
    });
    
    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '\"': '&quot;',
            \"'\": '&#039;'
        };
        return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
    }
    
    // Initial load and auto-refresh
    loadMessages();
    setInterval(loadMessages, 3000); // Refresh every 3 seconds
", \yii\web\View::POS_READY);
?>

<!-- Footer -->
<footer class="invitation-footer">
    <div class="container text-center">
        <p class="mb-2">Merupakan suatu kehormatan dan kebahagiaan bagi kami</p>
        <p class="mb-2">apabila Bapak/Ibu/Saudara/i berkenan hadir</p>
        <p class="mb-4">dan memberikan doa restu kepada kami.</p>
        
        <div class="footer-couple">
            <h4><?= Html::encode($invitation->bride_name) ?> & <?= Html::encode($invitation->groom_name) ?></h4>
        </div>
        
        <hr class="my-4">
        <p class="small" style="color: white;">
            &copy; <?= date('Y') ?> Wedding Invitation App
        </p>
    </div>
</footer>
