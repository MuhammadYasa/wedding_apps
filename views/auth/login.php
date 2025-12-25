<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login Admin';
?>

<div class="auth-login">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo/Title -->
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h3 class="mt-3 fw-bold">Admin Login</h3>
                            <p class="text-muted small">Wedding Apps Management</p>
                        </div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['class' => 'needs-validation'],
                            'fieldConfig' => [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'form-label fw-semibold'],
                                'inputOptions' => ['class' => 'form-control'],
                                'errorOptions' => ['class' => 'invalid-feedback d-block'],
                            ],
                        ]); ?>

                        <?= $form->field($model, 'username')->textInput([
                            'autofocus' => true,
                            'placeholder' => 'Masukkan username',
                            'class' => 'form-control form-control-lg'
                        ])->label('<i class="bi bi-person-fill me-1"></i> Username') ?>

                        <?= $form->field($model, 'password')->passwordInput([
                            'placeholder' => 'Masukkan password',
                            'class' => 'form-control form-control-lg'
                        ])->label('<i class="bi bi-lock-fill me-1"></i> Password') ?>

                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
                            'labelOptions' => ['class' => 'form-check-label'],
                            'inputOptions' => ['class' => 'form-check-input'],
                        ])->label('Ingat Saya') ?>

                        <div class="d-grid gap-2 mt-4">
                            <?= Html::submitButton('<i class="bi bi-box-arrow-in-right me-2"></i> Login', [
                                'class' => 'btn btn-primary btn-lg',
                                'name' => 'login-button'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <!-- Divider -->
                        <div class="position-relative my-4">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                atau
                            </span>
                        </div>

                        <!-- Google Login -->
                        <div class="d-grid">
                            <?= Html::a(
                                '<i class="bi bi-google me-2"></i> Continue with Google',
                                ['/auth/callback', 'authclient' => 'google'],
                                ['class' => 'btn btn-google btn-lg']
                            ) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.auth-login .card {
    border-radius: 15px;
}

.auth-login .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.auth-login .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
}

.auth-login .btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.auth-login .btn-google {
    background-color: #4285f4;
    color: white;
    border: none;
    font-weight: 500;
    font-size: 16px;
}

.auth-login .btn-google:hover {
    background-color: #357ae8;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 133, 244, 0.4);
    transition: all 0.3s ease;
}

.auth-login .btn-google i {
    background-color: white;
    color: #4285f4;
    border-radius: 50%;
    padding: 8px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.auth-login hr {
    margin: 0;
}
</style>
