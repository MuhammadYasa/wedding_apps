<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Invitation */

$this->title = 'Create Invitation';
?>
<div class="invitation-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
