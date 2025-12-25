<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Manage Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <p>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Create User', ['create'], ['class' => 'btn btn-success', 'encode' => false]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => 'No',
            ],

            [
                'attribute' => 'username',
                'label' => 'Username',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'label' => 'Email',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'role',
                'label' => 'Role',
                'value' => function($model) {
                    return $model->role === User::ROLE_SUPER_USER 
                        ? '<span class="badge bg-danger">Super User</span>' 
                        : '<span class="badge bg-primary">Client</span>';
                },
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'label' => 'Nama Pasangan',
                'value' => function($model) {
                    if (count($model->invitations) == 0) {
                        return '<span class="text-muted">-</span>';
                    }
                    $invitation = $model->invitations[0];
                    return $invitation->bride_name . ' & ' . $invitation->groom_name;
                },
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Created At',
                'format' => ['datetime', 'php:d M Y H:i'],
                'enableSorting' => false,
            ],
            [
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    // Super user doesn't have status
                    if ($model->role === User::ROLE_SUPER_USER) {
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
                        // Don't show toggle for own account or super user
                        if ($model->id == Yii::$app->user->id || $model->role === User::ROLE_SUPER_USER) {
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
                                'data-confirm' => 'Apakah Anda yakin ingin mengubah status user ini?',
                            ]
                        );
                    },
                    'delete' => function ($url, $model) {
                        if ($model->id == Yii::$app->user->id) {
                            return '';
                        }
                        return Html::a('<i class="bi bi-trash"></i>', $url, [
                            'title' => 'Delete',
                            'data-confirm' => 'Are you sure you want to delete this user?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'headerOptions' => ['style' => 'width: 120px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
            ],
        ],
    ]); ?>

</div>
