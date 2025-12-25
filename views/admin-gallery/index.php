<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use app\models\Invitation;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation|null */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $invitations app\models\Invitation[] */
/* @var $currentInvitation int|null */

$this->title = $invitation ? 'Gallery: ' . $invitation->title : 'Gallery Management';
if ($invitation) {
    $this->params['breadcrumbs'][] = ['label' => 'Undangan', 'url' => ['admin-invitation/index']];
    $this->params['breadcrumbs'][] = ['label' => $invitation->title, 'url' => ['admin-invitation/view', 'id' => $invitation->id]];
    $this->params['breadcrumbs'][] = 'Gallery';
} else {
    $this->params['breadcrumbs'][] = 'Gallery Management';
}

// Register custom CSS
$this->registerCss('
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 20px;
}
@media (max-width: 1200px) {
    .gallery-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 480px) {
    .gallery-grid {
        grid-template-columns: repeat(1, 1fr);
    }
}
.gallery-item {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    transition: transform 0.2s;
}
.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}
.gallery-item-info {
    padding: 10px;
}
.gallery-item-caption {
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
    min-height: 20px;
}
.gallery-item-order {
    font-size: 11px;
    color: #999;
    margin-bottom: 8px;
}
.gallery-item-actions {
    display: flex;
    gap: 5px;
}
');
?>

<div class="admin-gallery-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php 
        // For client: auto-get their invitation and show upload button
        if (Yii::$app->user->identity->isClient()) {
            $clientInvitation = Invitation::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->one();
            if ($clientInvitation) {
                echo Html::a('<i class="bi bi-plus-circle"></i> Upload Foto', ['create', 'invitation_id' => $clientInvitation->id], ['class' => 'btn btn-success']);
            }
        } elseif ($invitation) {
            // For super user with selected invitation
            echo Html::a('<i class="bi bi-plus-circle"></i> Tambah Foto', ['create', 'invitation_id' => $invitation->id], ['class' => 'btn btn-success']);
        }
        ?>
    </div>

    <!-- Filter Invitation - Only for Super User -->
    <?php if (!$invitation && Yii::$app->user->identity->isSuperUser()): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="get" action="<?= Url::to(['index']) ?>" class="row g-3">
                    <div class="col-md-10">
                        <label for="invitation_id" class="form-label">
                            <i class="bi bi-funnel me-1"></i>
                            Filter Undangan
                        </label>
                        <select name="invitation_id" id="invitation_id" class="form-select">
                            <option value="">Semua Gallery</option>
                            <?php foreach ($invitations as $inv): ?>
                                <option value="<?= $inv->id ?>" <?= $currentInvitation == $inv->id ? 'selected' : '' ?>>
                                    <?= Html::encode($inv->title) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($invitation): ?>

    <div class="gallery-grid">
        <?php foreach ($dataProvider->models as $index => $gallery): ?>
            <div class="gallery-item" data-id="<?= $gallery->id ?>">
                <img src="<?= $gallery->getThumbnailUrl() ?>" 
                     alt="<?= Html::encode($gallery->caption ?? '') ?>" 
                     onerror="this.src='<?= $gallery->getImageUrl() ?>'"
                     class="gallery-image-clickable"
                     data-full-url="<?= $gallery->getImageUrl() ?>"
                     data-caption="<?= Html::encode($gallery->caption ?? '') ?>"
                     data-index="<?= $index ?>"
                     style="cursor: pointer;">
                <div class="gallery-item-info">
                    <?php if ($gallery->caption): ?>
                        <div class="gallery-item-caption">
                            <?= Html::encode($gallery->caption) ?>
                        </div>
                    <?php endif; ?>
                    <div class="gallery-item-actions">
                        <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $gallery->id], [
                            'class' => 'btn btn-sm btn-danger',
                            'title' => 'Hapus',
                            'data' => [
                                'confirm' => 'Apakah Anda yakin ingin menghapus foto ini?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($dataProvider->models)): ?>
        <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle"></i> 
            Belum ada foto. Klik tombol "Tambah Foto" untuk mengunggah.
        </div>
    <?php endif; ?>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="lightboxCaption"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center justify-content-center p-0" style="position: relative;">
                    <button class="btn btn-light btn-lg" id="prevBtn" style="position: absolute; left: 20px; z-index: 1000;">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <img id="lightboxImage" src="" class="img-fluid" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
                    <button class="btn btn-light btn-lg" id="nextBtn" style="position: absolute; right: 20px; z-index: 1000;">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <span class="text-white" id="imageCounter"></span>
                </div>
            </div>
        </div>
    </div>

    <?php
    // JavaScript for lightbox functionality
    $this->registerJs("
        let currentImageIndex = 0;
        let images = [];
        
        // Collect all images
        $('.gallery-image-clickable').each(function(index) {
            images.push({
                url: $(this).data('full-url'),
                caption: $(this).data('caption'),
                index: index
            });
        });
        
        // Open lightbox on image click
        $('.gallery-image-clickable').click(function() {
            currentImageIndex = parseInt($(this).data('index'));
            showImage(currentImageIndex);
            $('#lightboxModal').modal('show');
        });
        
        // Show image function
        function showImage(index) {
            if (images.length > 0) {
                $('#lightboxImage').attr('src', images[index].url);
                $('#lightboxCaption').text(images[index].caption || 'Foto ' + (index + 1));
                $('#imageCounter').text((index + 1) + ' / ' + images.length);
                
                // Disable/enable buttons based on position
                $('#prevBtn').prop('disabled', index === 0);
                $('#nextBtn').prop('disabled', index === images.length - 1);
            }
        }
        
        // Previous button
        $('#prevBtn').click(function() {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                showImage(currentImageIndex);
            }
        });
        
        // Next button
        $('#nextBtn').click(function() {
            if (currentImageIndex < images.length - 1) {
                currentImageIndex++;
                showImage(currentImageIndex);
            }
        });
        
        // Keyboard navigation
        $(document).keydown(function(e) {
            if ($('#lightboxModal').hasClass('show')) {
                if (e.keyCode === 37) { // Left arrow
                    $('#prevBtn').click();
                } else if (e.keyCode === 39) { // Right arrow
                    $('#nextBtn').click();
                } else if (e.keyCode === 27) { // Escape
                    $('#lightboxModal').modal('hide');
                }
            }
        });
    ", \yii\web\View::POS_READY);
    ?>
    
    <?php else: ?>
    <!-- GridView for all galleries when no specific invitation selected -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'filename',
                'label' => 'Foto',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::img($model->getThumbnailUrl() ?: $model->getImageUrl(), [
                        'style' => 'width: 80px; height: 60px; object-fit: cover; border-radius: 5px;'
                    ]);
                },
            ],
            'caption',
            [
                'attribute' => 'invitation_id',
                'label' => 'Undangan',
                'value' => function ($model) {
                    return $model->invitation ? $model->invitation->title : '-';
                },
            ],
            'sort_order',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d M Y H:i'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-primary',
                            'title' => 'Edit',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-danger',
                            'title' => 'Hapus',
                            'data' => [
                                'confirm' => 'Apakah Anda yakin ingin menghapus foto ini?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <?php endif; ?>

</div>
