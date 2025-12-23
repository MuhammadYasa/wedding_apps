<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $galleries app\models\Gallery[] */
/* @var $invitation app\models\Invitation|null */

$this->title = $invitation ? 'Gallery - ' . $invitation->title : 'Gallery Foto';
?>

<div class="gallery-index-page">
    <div class="container py-5">
        
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">
                <i class="bi bi-images me-2"></i>
                Gallery Foto
            </h1>
            <?php if ($invitation): ?>
                <p class="lead text-muted">
                    <?= Html::encode($invitation->title) ?>
                </p>
            <?php else: ?>
                <p class="lead text-muted">
                    Koleksi Momen Indah Kami
                </p>
            <?php endif; ?>
        </div>

        <?php if (empty($galleries)): ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-image text-muted" style="font-size: 100px;"></i>
                <h3 class="mt-4 text-muted">Belum Ada Foto</h3>
                <p class="text-muted">Gallery foto masih kosong</p>
            </div>
        <?php else: ?>
            <!-- Gallery Grid -->
            <div class="row g-4" id="gallery-grid">
                <?php foreach ($galleries as $index => $gallery): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="gallery-item" data-index="<?= $index ?>">
                            <a href="<?= $gallery->getImageUrl() ?>" 
                               data-lightbox="gallery" 
                               data-title="<?= Html::encode($gallery->caption ?: 'Photo ' . ($index + 1)) ?>"
                               class="gallery-link">
                                <div class="gallery-image-wrapper">
                                    <img src="<?= $gallery->getThumbnailUrl() ?: $gallery->getImageUrl() ?>" 
                                         alt="<?= Html::encode($gallery->caption ?: 'Gallery Photo') ?>" 
                                         class="gallery-image"
                                         loading="lazy">
                                    <div class="gallery-overlay">
                                        <i class="bi bi-zoom-in"></i>
                                        <?php if ($gallery->caption): ?>
                                            <p class="caption-text"><?= Html::encode($gallery->caption) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Photo Counter -->
            <div class="text-center mt-5">
                <p class="text-muted">
                    <i class="bi bi-camera-fill me-2"></i>
                    Total <?= count($galleries) ?> Foto
                </p>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <?php if ($invitation): ?>
            <div class="text-center mt-5">
                <?= Html::a(
                    '<i class="bi bi-arrow-left me-2"></i>Kembali ke Undangan',
                    ['/invitation/view', 'slug' => $invitation->slug],
                    ['class' => 'btn btn-outline-primary btn-lg']
                ) ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
// Register Lightbox2 CSS & JS (CDN)
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css', [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js', [
    'depends' => [\yii\web\JqueryAsset::class]
]);

// Lightbox Options
$js = <<<JS
lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'albumLabel': 'Foto %1 dari %2',
    'fadeDuration': 300,
    'imageFadeDuration': 300
});
JS;
$this->registerJs($js);
?>

<style>
.gallery-index-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.gallery-image-wrapper {
    position: relative;
    width: 100%;
    padding-bottom: 100%; /* 1:1 Aspect Ratio */
    overflow: hidden;
    background: #f0f0f0;
}

.gallery-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-image {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(102, 126, 234, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    padding: 15px;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-overlay i {
    font-size: 48px;
    margin-bottom: 10px;
}

.caption-text {
    font-size: 14px;
    text-align: center;
    margin: 0;
    max-height: 3em;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
}

.gallery-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .gallery-overlay i {
        font-size: 32px;
    }
    
    .caption-text {
        font-size: 12px;
    }
}

/* Animation on load */
.gallery-item {
    animation: fadeInUp 0.5s ease-out;
    animation-fill-mode: both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for grid items */
<?php foreach ($galleries as $index => $gallery): ?>
.gallery-item:nth-child(<?= $index + 1 ?>) {
    animation-delay: <?= $index * 0.05 ?>s;
}
<?php endforeach; ?>
</style>
