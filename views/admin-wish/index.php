<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $invitations app\models\Invitation[] */
/* @var $selectedInvitation app\models\Invitation */
/* @var $totalWishes int */
/* @var $approvedWishes int */
/* @var $pendingWishes int */

$this->title = 'Kelola Ucapan & Buku Tamu';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="admin-wish-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-journal-heart"></i> <?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Invitation Filter -->
    <?php if (Yii::$app->user->identity->isSuperUser() && count($invitations) > 1): ?>
        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label"><strong>Filter Undangan:</strong></label>
                <select class="form-select" onchange="window.location.href='<?= Url::to(['index']) ?>?id=' + this.value">
                    <option value="">-- Semua Undangan --</option>
                    <?php foreach ($invitations as $inv): ?>
                        <option value="<?= $inv->id ?>" <?= $selectedInvitation && $selectedInvitation->id == $inv->id ? 'selected' : '' ?>>
                            <?= Html::encode($inv->title) ?> - <?= Yii::$app->formatter->asDate($inv->event_date) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h2 class="text-primary mb-0"><?= $totalWishes ?></h2>
                    <p class="text-muted mb-0">Total Ucapan</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h2 class="text-success mb-0"><?= $approvedWishes ?></h2>
                    <p class="text-muted mb-0">Disetujui</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h2 class="text-warning mb-0"><?= $pendingWishes ?></h2>
                    <p class="text-muted mb-0">Menunggu Persetujuan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Wishes Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Ucapan</h5>
        </div>
        <div class="card-body">
            <?php $form = Html::beginForm(['bulk-approve'], 'post', ['id' => 'bulk-form']); ?>
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => "{items}\n{pager}",
                'tableOptions' => ['class' => 'table table-hover'],
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model) {
                            return ['value' => $model->id];
                        }
                    ],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 60px'],
                    ],
                    [
                        'attribute' => 'invitation_id',
                        'label' => 'Undangan',
                        'value' => function($model) {
                            return $model->invitation ? $model->invitation->title : '-';
                        },
                        'visible' => !$selectedInvitation,
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'html',
                        'value' => function($model) {
                            $html = '<strong>' . Html::encode($model->name) . '</strong>';
                            if ($model->email) {
                                $html .= '<br><small class="text-muted"><i class="bi bi-envelope"></i> ' . Html::encode($model->email) . '</small>';
                            }
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'message',
                        'format' => 'html',
                        'value' => function($model) {
                            return '<div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis;">' 
                                . Html::encode($model->getShortMessage(150)) 
                                . '</div>';
                        },
                    ],
                    [
                        'attribute' => 'is_approved',
                        'format' => 'html',
                        'value' => function($model) {
                            return $model->is_approved 
                                ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>'
                                : '<span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>';
                        },
                        'headerOptions' => ['style' => 'width: 120px'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:d M Y, H:i'],
                        'headerOptions' => ['style' => 'width: 150px'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{approve} {view} {update} {delete}',
                        'buttons' => [
                            'approve' => function ($url, $model) {
                                if ($model->is_approved) {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-check-circle"></i>', 
                                    ['approve', 'id' => $model->id], 
                                    [
                                        'class' => 'btn btn-sm btn-success',
                                        'title' => 'Setujui',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Setujui ucapan ini?',
                                    ]
                                );
                            },
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', 
                                    ['view', 'id' => $model->id], 
                                    ['class' => 'btn btn-sm btn-info', 'title' => 'Lihat']
                                );
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<i class="bi bi-pencil"></i>', 
                                    ['update', 'id' => $model->id], 
                                    ['class' => 'btn btn-sm btn-primary', 'title' => 'Edit']
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<i class="bi bi-trash"></i>', 
                                    ['delete', 'id' => $model->id], 
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'Hapus',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Hapus ucapan ini?',
                                    ]
                                );
                            },
                        ],
                        'headerOptions' => ['style' => 'width: 200px'],
                    ],
                ],
            ]); ?>

            <!-- Bulk Actions -->
            <div class="mt-3">
                <div class="btn-group">
                    <?= Html::button('<i class="bi bi-check-all"></i> Setujui Terpilih', [
                        'class' => 'btn btn-success',
                        'onclick' => 'submitBulkAction("bulk-approve")'
                    ]) ?>
                    <?= Html::button('<i class="bi bi-trash"></i> Hapus Terpilih', [
                        'class' => 'btn btn-danger',
                        'onclick' => 'submitBulkAction("bulk-delete")'
                    ]) ?>
                </div>
            </div>

            <?php Html::endForm(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function submitBulkAction(action) {
    var form = $('#bulk-form');
    var checked = $('input[name="selection[]"]:checked').length;
    
    if (checked === 0) {
        alert('Pilih minimal satu ucapan');
        return false;
    }
    
    var confirmMsg = action === 'bulk-approve' 
        ? 'Setujui ' + checked + ' ucapan terpilih?' 
        : 'Hapus ' + checked + ' ucapan terpilih?';
    
    if (confirm(confirmMsg)) {
        form.attr('action', '" . Url::to(['']) . "' + action);
        form.submit();
    }
}
JS
);
?>

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
