<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Rsvp */

$this->title = 'RSVP Berhasil Dikirim';
?>

<div class="rsvp-confirmation-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <!-- Success Animation -->
                <div class="text-center mb-4">
                    <div class="success-checkmark">
                        <div class="check-icon">
                            <span class="icon-line line-tip"></span>
                            <span class="icon-line line-long"></span>
                            <div class="icon-circle"></div>
                            <div class="icon-fix"></div>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5 text-center">
                        
                        <h1 class="display-5 fw-bold mb-3 text-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Terima Kasih!
                        </h1>
                        
                        <p class="lead mb-4">
                            RSVP Anda telah berhasil dikirim
                        </p>

                        <div class="confirmation-details bg-light p-4 rounded mb-4">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Detail Konfirmasi
                            </h5>
                            
                            <div class="row text-start">
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">Nama</small>
                                    <strong><?= Html::encode($model->name) ?></strong>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">Email</small>
                                    <strong><?= Html::encode($model->email) ?></strong>
                                </div>

                                <?php if ($model->phone): ?>
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">No. Telepon</small>
                                    <strong><?= Html::encode($model->phone) ?></strong>
                                </div>
                                <?php endif; ?>
                                
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">Status Kehadiran</small>
                                    <?php if ($model->attendance === 'attending'): ?>
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Hadir
                                        </span>
                                        <?php if ($model->guests_count > 0): ?>
                                            <small class="text-muted ms-2">(<?= $model->guests_count ?> orang)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Tidak Hadir
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($model->message): ?>
                                <div class="col-12 mb-3">
                                    <small class="text-muted d-block">Pesan & Doa</small>
                                    <div class="alert alert-light mb-0">
                                        <i class="bi bi-chat-quote me-2"></i>
                                        <em><?= Html::encode($model->message) ?></em>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="col-12">
                                    <small class="text-muted d-block">Tanggal Pengiriman</small>
                                    <strong><?= Yii::$app->formatter->asDatetime($model->created_at) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Info Message -->
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="bi bi-envelope-check me-2"></i>
                            Email konfirmasi telah dikirim ke <strong><?= Html::encode($model->email) ?></strong>
                        </div>

                        <!-- Token Info (for tracking) -->
                        <p class="text-muted small mb-4">
                            <i class="bi bi-shield-check me-1"></i>
                            Kode Konfirmasi: <code><?= Html::encode($model->token) ?></code>
                        </p>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <?= Html::a(
                                '<i class="bi bi-house-door me-2"></i>Kembali ke Beranda',
                                ['/site/index'],
                                ['class' => 'btn btn-primary btn-lg px-4']
                            ) ?>
                            
                            <?= Html::a(
                                '<i class="bi bi-images me-2"></i>Lihat Gallery',
                                ['/gallery/index'],
                                ['class' => 'btn btn-outline-secondary btn-lg px-4']
                            ) ?>
                        </div>

                    </div>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="bi bi-question-circle me-2"></i>
                        Ada pertanyaan? Hubungi kami di 
                        <a href="mailto:info@wedding.com" class="text-decoration-none">info@wedding.com</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.rsvp-confirmation-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.card {
    border-radius: 15px;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }
    to {
        transform: scale(1);
    }
}

.check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #4CAF50;
    background: white;
}

.icon-line {
    height: 5px;
    background-color: #4CAF50;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.icon-line.line-tip {
    top: 40px;
    left: 18px;
    width: 25px;
    transform: rotate(45deg);
    animation: checkmarkTip 0.75s;
}

.icon-line.line-long {
    top: 35px;
    right: 10px;
    width: 45px;
    transform: rotate(-45deg);
    animation: checkmarkLong 0.75s;
}

@keyframes checkmarkTip {
    0% {
        width: 0;
        left: 5px;
        top: 20px;
    }
    50% {
        width: 0;
        left: 5px;
        top: 20px;
    }
    100% {
        width: 25px;
        left: 18px;
        top: 40px;
    }
}

@keyframes checkmarkLong {
    0% {
        width: 0;
        right: 45px;
        top: 45px;
    }
    65% {
        width: 0;
        right: 45px;
        top: 45px;
    }
    100% {
        width: 45px;
        right: 10px;
        top: 35px;
    }
}

.icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(76, 175, 80, .2);
}

.icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: white;
}

.confirmation-details {
    border-left: 4px solid #4CAF50;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.btn-outline-secondary {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
}
</style>
