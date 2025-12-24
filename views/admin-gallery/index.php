<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

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

// Register SortableJS CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Register custom gallery sortable script
$this->registerJsFile('@web/js/gallery-sortable.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Initialize sortable with sort URL
$sortUrl = Url::to(['sort']);
$this->registerJs("initGallerySortable('$sortUrl');", \yii\web\View::POS_READY);

// Register custom CSS
$this->registerCss('
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.gallery-item {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    cursor: move;
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
.sortable-ghost {
    opacity: 0.4;
}
');
?>

<div class="admin-gallery-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($invitation): ?>
            <?= Html::a('<i class="bi bi-plus-circle"></i> Tambah Foto', ['create', 'invitation_id' => $invitation->id], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <!-- Filter Invitation -->
    <?php if (!$invitation): ?>
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
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        <strong>Tips:</strong> Drag & drop foto untuk mengubah urutan tampilan.
    </div>

    <div class="gallery-grid" id="gallery-sortable">
        <?php foreach ($dataProvider->models as $gallery): ?>
            <div class="gallery-item" data-id="<?= $gallery->id ?>">
                <img src="<?= $gallery->getThumbnailUrl() ?>" alt="<?= Html::encode($gallery->caption ?? '') ?>" onerror="this.src='<?= $gallery->getImageUrl() ?>'">
                <div class="gallery-item-info">
                    <div class="gallery-item-caption">
                        <?= Html::encode($gallery->caption ?: '(Tanpa caption)') ?>
                    </div>
                    <div class="gallery-item-order">
                        <i class="bi bi-arrows-move"></i> Urutan: <?= $gallery->sort_order ?>
                    </div>
                    <div class="gallery-item-actions">
                        <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $gallery->id], [
                            'class' => 'btn btn-sm btn-primary',
                            'title' => 'Edit'
                        ]) ?>
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
