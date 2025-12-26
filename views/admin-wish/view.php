<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Wish */

$this->title = 'Detail Ucapan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kelola Ucapan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="wish-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-eye"></i> <?= Html::encode($this->title) ?></h1>
        <div>
            <?php if (!$model->is_approved): ?>
                <?= Html::a('<i class="bi bi-check-circle"></i> Setujui', ['approve', 'id' => $model->id], [
                    'class' => 'btn btn-success',
                    'data-method' => 'post',
                    'data-confirm' => 'Setujui ucapan ini?'
                ]) ?>
            <?php endif; ?>
            <?= Html::a('<i class="bi bi-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-trash"></i> Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data-method' => 'post',
                'data-confirm' => 'Hapus ucapan ini?'
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Detail Ucapan</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped detail-view'],
                        'attributes' => [
                            'id',
                            [
                                'attribute' => 'invitation_id',
                                'label' => 'Undangan',
                                'value' => $model->invitation ? $model->invitation->title : '-',
                            ],
                            [
                                'attribute' => 'guest_id',
                                'label' => 'Tamu Terdaftar',
                                'value' => $model->guest ? $model->guest->name : 'Tamu Umum',
                            ],
                            'name',
                            'email:email',
                            [
                                'attribute' => 'message',
                                'format' => 'ntext',
                                'value' => $model->message,
                            ],
                            [
                                'attribute' => 'is_approved',
                                'format' => 'html',
                                'value' => $model->is_approved 
                                    ? '<span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Disetujui</span>'
                                    : '<span class="badge bg-warning fs-6"><i class="bi bi-clock"></i> Menunggu Persetujuan</span>',
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-card-text"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <?= $this->render('../wish/_wish_card', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::a('<i class="bi bi-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
    margin-bottom: 20px;
}
.card-header {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}
</style>
