<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Invitation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invitation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bride_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'groom_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bride_nickname')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Siti']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'groom_nickname')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Ahmad']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bride_father')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'groom_father')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'bride_mother')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'groom_mother')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group field-invitation-event_date<?= $model->hasErrors('event_date') ? ' has-error' : '' ?>">
                <label class="control-label" for="event-date-display">Tanggal Acara</label>
                <input type="date" id="event-date-display" class="form-control" 
                       value="<?= $model->event_date ? date('Y-m-d', $model->event_date) : '' ?>">
                <?= Html::activeHiddenInput($model, 'event_date') ?>
                <?php if ($model->hasErrors('event_date')): ?>
                    <p class="help-block help-block-error"><?= $model->getFirstError('event_date') ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'event_time')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: 10:00 - 12:00']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'theme')->dropDownList([
                'default' => 'Default',
                'elegant' => 'Elegant',
                'rustic' => 'Rustic',
                'modern' => 'Modern',
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'venue')->textInput(['maxlength' => true, 'placeholder' => 'Nama tempat acara']) ?>

    <?= $form->field($model, 'venue_address')->textarea(['rows' => 3, 'placeholder' => 'Alamat lengkap']) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'venue_lat')->textInput(['placeholder' => 'Latitude, contoh: -6.200000']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'venue_lng')->textInput(['placeholder' => 'Longitude, contoh: 106.816666']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'venue_map_url')->textInput(['maxlength' => true, 'placeholder' => 'Google Maps URL']) ?>
        </div>
    </div>

    <?= $form->field($model, 'story')->textarea(['rows' => 4, 'placeholder' => 'Cerita perjalanan cinta pasangan...']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Deskripsi singkat acara']) ?>

    <?php if (Yii::$app->user->identity->isSuperUser()): ?>
        <?= $form->field($model, 'user_id')->dropDownList(
            \yii\helpers\ArrayHelper::map(User::find()->where(['role' => User::ROLE_CLIENT])->all(), 'id', 'username'),
            ['prompt' => 'Pilih Client Owner']
        )->hint('Pilih user client yang memiliki undangan ini') ?>
    <?php endif; ?>

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

<?php
// Convert date input to timestamp
$this->registerJs("
    // Set initial timestamp value
    var initialDate = $('#event-date-display').val();
    if (initialDate) {
        var timestamp = Math.floor(new Date(initialDate + 'T00:00:00').getTime() / 1000);
        $('#invitation-event_date').val(timestamp);
    }
    
    // Update timestamp when date changes
    $('#event-date-display').on('change', function() {
        var dateInput = $(this).val();
        if (dateInput) {
            var timestamp = Math.floor(new Date(dateInput + 'T00:00:00').getTime() / 1000);
            $('#invitation-event_date').val(timestamp);
        }
    });
");
?>
