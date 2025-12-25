<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->hint(
                $model->isNewRecord 
                    ? 'Minimal 6 karakter' 
                    : 'Kosongkan jika tidak ingin mengubah password'
            ) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'role')->dropDownList([
                User::ROLE_SUPER_USER => 'Super User',
                User::ROLE_CLIENT => 'Client',
            ], ['prompt' => 'Select Role', 'id' => 'user-role']) ?>
        </div>
    </div>

    <div id="couple-names-section" style="<?= $model->isNewRecord ? '' : 'display:none;' ?>">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-1"></i> <strong>Nama Pasangan</strong> akan digunakan untuk membuat undangan pernikahan otomatis.
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'bride_name')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Siti Nurhaliza'])->label('Nama Mempelai Wanita') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'groom_name')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Ahmad Dhani'])->label('Nama Mempelai Pria') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'bride_nickname')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Siti'])->label('Nama Panggilan Mempelai Wanita') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'groom_nickname')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Dhani'])->label('Nama Panggilan Mempelai Pria') ?>
            </div>
        </div>
    </div>

    <?php
    $this->registerJs("
        $('#user-role').on('change', function() {
            if ($(this).val() === 'client') {
                $('#couple-names-section').slideDown();
            } else {
                $('#couple-names-section').slideUp();
            }
        });
        
        // Trigger on page load for edit mode
        if ($('#user-role').val() === 'client') {
            $('#couple-names-section').show();
        }
    ");
    ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> ' . ($model->isNewRecord ? 'Create' : 'Update'), [
            'class' => 'btn btn-success',
            'encode' => false,
        ]) ?>
        <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancel', ['index'], [
            'class' => 'btn btn-secondary',
            'encode' => false,
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
