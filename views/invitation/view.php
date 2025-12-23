<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guest app\models\Guest|null */
/* @var $galleries app\models\Gallery[] */
/* @var $recentRsvps app\models\Rsvp[] */

$this->title = $invitation->title;

// Register CSS
$this->registerCssFile('@web/css/invitation.css', ['depends' => [\yii\bootstrap5\BootstrapAsset::class]]);

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
                <div class="gallery-item">
                    <img src="<?= $gallery->getImageUrl() ?>" 
                         alt="<?= Html::encode($gallery->caption ?? '') ?>" 
                         class="img-fluid">
                    <?php if ($gallery->caption): ?>
                    <div class="gallery-caption">
                        <?= Html::encode($gallery->caption) ?>
                    </div>
                    <?php endif; ?>
                </div>
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
            <div class="col-md-6">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Form RSVP akan segera tersedia. Terima kasih!
                </div>
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

<!-- Footer -->
<footer class="invitation-footer">
    <div class="container text-center">
        <p class="mb-2">Merupakan suatu kehormatan dan kebahagiaan bagi kami</p>
        <p class="mb-2">apabila Bapak/Ibu/Saudara/i berkenan hadir</p>
        <p class="mb-4">dan memberikan doa restu kepada kami.</p>
        
        <div class="footer-couple">
            <h4><?= Html::encode($invitation->bride_name) ?> & <?= Html::encode($invitation->groom_name) ?></h4>
        </div>
        
        <div class="footer-share mt-4">
            <p>Bagikan undangan ini:</p>
            <div class="share-buttons">
                <a href="<?= $invitation->getWhatsAppUrl($guest ? $guest->name : null) ?>" 
                   target="_blank" 
                   class="btn btn-sm btn-success">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($invitation->getUrl()) ?>" 
                   target="_blank" 
                   class="btn btn-sm btn-primary">
                    <i class="bi bi-facebook"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($invitation->getUrl()) ?>&text=<?= urlencode($invitation->title) ?>" 
                   target="_blank" 
                   class="btn btn-sm btn-info text-white">
                    <i class="bi bi-twitter"></i> Twitter
                </a>
            </div>
        </div>
        
        <hr class="my-4">
        <p class="small text-muted">
            &copy; <?= date('Y') ?> Wedding Invitation App
        </p>
    </div>
</footer>
