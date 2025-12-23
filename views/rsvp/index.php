<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Rsvp;

/* @var $this yii\web\View */
/* @var $model app\models\Rsvp */
/* @var $invitation app\models\Invitation */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'RSVP - ' . $invitation->title;
?>

<div class="rsvp-form-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="bi bi-envelope-heart me-2"></i>
                        Konfirmasi Kehadiran
                    </h1>
                    <p class="lead text-muted">
                        <?= Html::encode($invitation->title) ?>
                    </p>
                    <p class="text-muted">
                        <i class="bi bi-calendar-event me-2"></i>
                        <?= Yii::$app->formatter->asDate($invitation->event_date, 'long') ?>
                    </p>
                </div>

                <!-- RSVP Form Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">
                        
                        <?php $form = ActiveForm::begin([
                            'id' => 'rsvp-form',
                            'action' => ['rsvp/submit'],
                            'options' => ['class' => 'needs-validation'],
                            'enableClientValidation' => true,
                        ]); ?>

                        <?= $form->field($model, 'invitation_id')->hiddenInput()->label(false) ?>

                        <!-- Name Field -->
                        <?= $form->field($model, 'name')->textInput([
                            'maxlength' => true,
                            'placeholder' => 'Masukkan nama lengkap Anda',
                            'autofocus' => true,
                        ])->label('<i class="bi bi-person me-2"></i>Nama Lengkap') ?>

                        <!-- Email Field -->
                        <?= $form->field($model, 'email')->textInput([
                            'maxlength' => true,
                            'type' => 'email',
                            'placeholder' => 'contoh@email.com',
                        ])->label('<i class="bi bi-envelope me-2"></i>Email') ?>

                        <!-- Phone Field -->
                        <?= $form->field($model, 'phone')->textInput([
                            'maxlength' => true,
                            'placeholder' => '08xxxxxxxxxx',
                        ])->label('<i class="bi bi-telephone me-2"></i>No. Telepon / WhatsApp')->hint('Opsional') ?>

                        <!-- Attendance Radio -->
                        <?= $form->field($model, 'attendance')->radioList(
                            Rsvp::getAttendanceOptions(),
                            [
                                'item' => function ($index, $label, $name, $checked, $value) {
                                    $icon = $value === Rsvp::ATTENDANCE_ATTENDING 
                                        ? '<i class="bi bi-check-circle text-success me-2"></i>'
                                        : '<i class="bi bi-x-circle text-danger me-2"></i>';
                                    
                                    return '<div class="form-check mb-3">
                                        <input type="radio" id="' . $name . $index . '" class="form-check-input" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>
                                        <label class="form-check-label" for="' . $name . $index . '">
                                            ' . $icon . $label . '
                                        </label>
                                    </div>';
                                },
                            ]
                        )->label('<i class="bi bi-calendar-check me-2"></i>Apakah Anda akan hadir?') ?>

                        <!-- Guests Count (only show if attending) -->
                        <div id="guests-count-field" style="display: none;">
                            <?= $form->field($model, 'guests_count')->textInput([
                                'type' => 'number',
                                'min' => 1,
                                'max' => 10,
                                'value' => 1,
                            ])->label('<i class="bi bi-people me-2"></i>Jumlah Tamu yang Hadir')->hint('Termasuk Anda sendiri (maksimal 10 orang)') ?>
                        </div>

                        <!-- Message Field -->
                        <?= $form->field($model, 'message')->textarea([
                            'rows' => 4,
                            'placeholder' => 'Ucapan selamat, doa, atau pesan untuk mempelai...',
                        ])->label('<i class="bi bi-chat-heart me-2"></i>Pesan & Doa')->hint('Opsional') ?>

                        <!-- Submit Button -->
                        <div class="form-group mt-4">
                            <?= Html::submitButton('<i class="bi bi-send me-2"></i>Kirim RSVP', [
                                'class' => 'btn btn-primary btn-lg w-100',
                                'id' => 'submit-btn',
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Informasi:</strong> Dengan mengirimkan RSVP ini, Anda akan menerima konfirmasi melalui email yang Anda daftarkan.
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Show/hide guests count based on attendance selection
$(document).ready(function() {
    $('input[name="Rsvp[attendance]"]').change(function() {
        if ($(this).val() === 'attending') {
            $('#guests-count-field').slideDown();
        } else {
            $('#guests-count-field').slideUp();
            $('#rsvp-guests_count').val('1');
        }
    });

    // Trigger on page load if attending is pre-selected
    if ($('input[name="Rsvp[attendance]"]:checked').val() === 'attending') {
        $('#guests-count-field').show();
    }
});
JS;
$this->registerJs($js);
?>

<style>
.rsvp-form-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.card {
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 15px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #004085;
    border-radius: 10px;
}
</style>
