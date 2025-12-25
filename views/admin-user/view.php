<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="bi bi-pencil me-1"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary', 'encode' => false]) ?>
        <?php if ($model->id != Yii::$app->user->id): ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'encode' => false,
                'data' => [
                    'confirm' => 'Are you sure you want to delete this user?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'role',
                'value' => $model->role === User::ROLE_SUPER_USER 
                    ? '<span class="badge bg-danger">Super User</span>' 
                    : '<span class="badge bg-primary">Client</span>',
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d M Y H:i'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:d M Y H:i'],
            ],
        ],
    ]) ?>

    <h3 class="mt-4">Invitations (<?= count($model->invitations) ?>)</h3>
    
    <?php if (count($model->invitations) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Couple</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->invitations as $invitation): ?>
                        <tr>
                            <td><?= $invitation->id ?></td>
                            <td><?= Html::encode($invitation->title) ?></td>
                            <td><?= Html::encode($invitation->bride_name . ' & ' . $invitation->groom_name) ?></td>
                            <td><?= Yii::$app->formatter->asDate($invitation->event_date, 'php:d M Y') ?></td>
                            <td>
                                <?php if ($invitation->is_active): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= Html::a('View', ['/admin-invitation/view', 'id' => $invitation->id], ['class' => 'btn btn-sm btn-info']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted">No invitations assigned to this user.</p>
    <?php endif; ?>

</div>
