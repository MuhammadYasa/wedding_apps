<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Buku Tamu Digital - ' . $invitation->title;
$this->params['breadcrumbs'][] = ['label' => 'Undangan', 'url' => ['invitation/view', 'slug' => $invitation->slug]];
$this->params['breadcrumbs'][] = 'Buku Tamu';
?>

<div class="wish-index">
    <!-- Header Section -->
    <div class="text-center mb-5">
        <h1 class="display-4 mb-3">
            <i class="bi bi-journal-heart text-primary"></i> Buku Tamu Digital
        </h1>
        <p class="lead text-muted"><?= Html::encode($invitation->title) ?></p>
        <p class="text-muted">Tinggalkan pesan dan ucapan terbaik Anda untuk pengantin</p>
        
        <div class="mt-4">
            <?= Html::a('<i class="bi bi-pen-fill me-2"></i> Tulis Ucapan', 
                ['create', 'id' => $invitation->id], 
                ['class' => 'btn btn-primary btn-lg pulse-button']) ?>
        </div>
    </div>

    <!-- Wishes Wall -->
    <div class="wishes-wall">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_wish_card',
            'viewParams' => ['invitation' => $invitation],
            'layout' => "{summary}\n<div class='row g-4'>{items}</div>\n{pager}",
            'itemOptions' => ['class' => 'col-md-6 col-lg-4'],
            'emptyText' => '<div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">Belum ada ucapan. Jadilah yang pertama!</p>
                <a href="' . Url::to(['create', 'id' => $invitation->id]) . '" class="btn btn-primary mt-3">
                    <i class="bi bi-pen"></i> Tulis Ucapan Pertama
                </a>
            </div>',
            'summary' => '<p class="text-muted mb-4">Menampilkan {begin}-{end} dari {totalCount} ucapan</p>',
            'pager' => [
                'class' => 'yii\bootstrap5\LinkPager',
                'options' => ['class' => 'pagination justify-content-center mt-4'],
            ],
        ]) ?>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-5">
        <?= Html::a('<i class="bi bi-arrow-left me-2"></i> Kembali ke Undangan', 
            ['invitation/view', 'slug' => $invitation->slug], 
            ['class' => 'btn btn-outline-secondary']) ?>
    </div>
</div>

<style>
.wishes-wall {
    margin-top: 3rem;
}

.pulse-button {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
    }
}
</style>
