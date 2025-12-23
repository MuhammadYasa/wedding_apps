<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Guest $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kelola Tamu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-guest-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-person-fill me-2"></i><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="bi bi-pencil me-1"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> Hapus', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus tamu ini?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => '<i class="bi bi-person-fill me-1"></i>' . Html::encode($model->name),
                    ],
                    [
                        'attribute' => 'email',
                        'format' => 'raw',
                        'value' => $model->email ? '<i class="bi bi-envelope-fill me-1"></i>' . Html::encode($model->email) : '<span class="text-muted">-</span>',
                    ],
                    [
                        'attribute' => 'phone',
                        'format' => 'raw',
                        'value' => $model->phone ? '<i class="bi bi-telephone-fill me-1"></i>' . Html::encode($model->phone) : '<span class="text-muted">-</span>',
                    ],
                    [
                        'attribute' => 'invitation_id',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->invitation) {
                                return '<span class="badge bg-info">' . 
                                       Html::encode($model->invitation->bride_name . ' & ' . $model->invitation->groom_name) . 
                                       '</span>';
                            }
                            return '<span class="text-muted">-</span>';
                        },
                        'label' => 'Undangan',
                    ],
                    'token',
                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:Y-m-d H:i:s'],
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => ['datetime', 'php:Y-m-d H:i:s'],
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

</div>
