<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\models\Invitation;

/** @var yii\web\View $this */
/** @var app\models\Guest $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->title = 'Edit Tamu: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kelola Tamu', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="admin-guest-update">

    <h1><i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name')->textInput([
                'maxlength' => true,
                'placeholder' => 'Nama lengkap tamu'
            ])->label('<i class="bi bi-person-fill me-1"></i> Nama') ?>

            <?= $form->field($model, 'email')->textInput([
                'maxlength' => true,
                'placeholder' => 'email@example.com'
            ])->label('<i class="bi bi-envelope-fill me-1"></i> Email') ?>

            <?= $form->field($model, 'phone')->textInput([
                'maxlength' => true,
                'placeholder' => '08123456789'
            ])->label('<i class="bi bi-telephone-fill me-1"></i> Telepon') ?>

            <?= $form->field($model, 'invitation_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(Invitation::find()->all(), 'id', function($invitation) {
                    return $invitation->bride_name . ' & ' . $invitation->groom_name;
                }),
                ['prompt' => 'Pilih Undangan']
            )->label('<i class="bi bi-heart-fill me-1"></i> Undangan') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="bi bi-x-circle me-1"></i> Batal', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
