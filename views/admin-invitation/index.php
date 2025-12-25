<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invitations';
?>
<div class="invitation-index">

    <p>
        <?php if (Yii::$app->user->identity->isSuperUser()): ?>
            <?= Html::a('Create Invitation', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => 'No',
            ],

            [
                'attribute' => 'title',
                'label' => 'Judul Undangan',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'bride_name',
                'label' => 'Nama Mempelai Wanita',
                'value' => function($model) {
                    return $model->bride_name . ' & ' . $model->groom_name;
                },
                'enableSorting' => false,
            ],
            [
                'attribute' => 'user_id',
                'label' => 'User Id',
                'value' => 'user.username',
                'visible' => Yii::$app->user->identity->isSuperUser(),
                'enableSorting' => false,
            ],
            [
                'attribute' => 'event_date',
                'label' => 'Tanggal Acara',
                'format' => ['date', 'php:d M Y'],
                'enableSorting' => false,
            ],
            [
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    // Super user invitations don't have status management
                    if ($model->user && $model->user->role === User::ROLE_SUPER_USER) {
                        return '<span class="text-muted">-</span>';
                    }
                    
                    $statusText = $model->is_active ? 'Aktif' : 'Non-aktif';
                    $badgeClass = $model->is_active ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
                },
                'contentOptions' => ['style' => 'width: 100px; text-align: center;'],
                'enableSorting' => false,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Aksi',
                'template' => '{toggle} {view} {update} {delete}',
                'buttons' => [
                    'toggle' => function ($url, $model) {
                        // Hide toggle button for super user invitations
                        if ($model->user && $model->user->role === User::ROLE_SUPER_USER) {
                            return '';
                        }
                        
                        $icon = $model->is_active ? 'bi-toggle-on' : 'bi-toggle-off';
                        $color = $model->is_active ? 'text-success' : 'text-secondary';
                        $title = $model->is_active ? 'Nonaktifkan' : 'Aktifkan';
                        return Html::a(
                            '<i class="bi ' . $icon . ' ' . $color . '"></i>',
                            ['toggle-status', 'id' => $model->id],
                            [
                                'title' => $title,
                                'data-method' => 'post',
                                'data-confirm' => 'Apakah Anda yakin ingin mengubah status undangan ini?',
                            ]
                        );
                    },
                ],
                'headerOptions' => ['style' => 'width: 120px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
        ],
    ]); ?>


</div>
