<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Invitation */

$this->title = 'Update Invitation: ' . $model->title;
?>
<div class="invitation-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
