<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Invitation */

$this->title = $model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="invitation-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if (Yii::$app->user->identity->isSuperUser()): ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this invitation?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?= Html::a('View Public Page', $model->getUrl(), ['class' => 'btn btn-success', 'target' => '_blank']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'slug',
            'bride_name',
            'groom_name',
            'bride_father',
            'bride_mother',
            'groom_father',
            'groom_mother',
            [
                'attribute' => 'event_date',
                'format' => ['date', 'php:d M Y'],
            ],
            'event_time',
            'venue:ntext',
            'venue_address:ntext',
            'venue_map_url:url',
            'venue_lat',
            'venue_lng',
            'cover_image',
            'story:ntext',
            'description:ntext',
            'theme',
            'is_active:boolean',
            [
                'attribute' => 'user_id',
                'value' => $model->user ? $model->user->username : null,
                'visible' => Yii::$app->user->identity->isSuperUser(),
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

</div>
