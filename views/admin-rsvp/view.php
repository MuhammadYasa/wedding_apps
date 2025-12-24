<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Rsvp */

$this->title = 'Detail RSVP: ' . $model->name;
?>

<div class="admin-rsvp-view">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-person-check me-2"></i>
            Detail RSVP
        </h1>
        <div>
            <?= Html::a(
                '<i class="bi bi-arrow-left me-2"></i>Kembali',
                ['index'],
                ['class' => 'btn btn-secondary']
            ) ?>
            <?= Html::a(
                '<i class="bi bi-trash me-2"></i>Hapus',
                ['delete', 'id' => $model->id],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Yakin ingin menghapus RSVP ini?',
                        'method' => 'post',
                    ],
                ]
            ) ?>
        </div>
    </div>

    <div class="row">
        <!-- Main Information Card -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi RSVP
                    </h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view'],
                        'attributes' => [
                            [
                                'attribute' => 'name',
                                'label' => 'Nama Lengkap',
                                'format' => 'html',
                                'value' => '<strong>' . Html::encode($model->name) . '</strong>',
                            ],
                            'email:email',
                            [
                                'attribute' => 'phone',
                                'label' => 'Telepon',
                                'value' => $model->phone ?: '-',
                            ],
                            [
                                'attribute' => 'invitation_id',
                                'label' => 'Undangan',
                                'format' => 'html',
                                'value' => $model->invitation ? 
                                    Html::a(
                                        Html::encode($model->invitation->title),
                                        ['/invitation/view', 'slug' => $model->invitation->slug],
                                        ['target' => '_blank', 'class' => 'text-decoration-none']
                                    ) : '-',
                            ],
                            [
                                'attribute' => 'attendance',
                                'label' => 'Status Kehadiran',
                                'format' => 'html',
                                'value' => $model->attendance === \app\models\Rsvp::ATTENDANCE_ATTENDING
                                    ? '<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Hadir</span>'
                                    : '<span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Tidak Hadir</span>',
                            ],
                            [
                                'attribute' => 'guests_count',
                                'label' => 'Jumlah Tamu',
                                'format' => 'html',
                                'value' => $model->guests_count 
                                    ? '<span class="badge bg-info fs-6"><i class="bi bi-people me-1"></i>' . $model->guests_count . ' orang</span>'
                                    : '<span class="text-muted">-</span>',
                            ],
                            [
                                'attribute' => 'created_at',
                                'label' => 'Tanggal RSVP',
                                'format' => ['date', 'php:d F Y, H:i:s'],
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Message Card -->
            <?php if ($model->message): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-quote me-2"></i>
                            Pesan & Doa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="message-box">
                            <i class="bi bi-quote text-muted" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="mb-0 mt-3" style="font-size: 16px; line-height: 1.8;">
                                <?= nl2br(Html::encode($model->message)) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Ringkasan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="avatar-circle bg-primary">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">Nama</small>
                            <div class="fw-bold"><?= Html::encode($model->name) ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="me-3">
                            <div class="avatar-circle <?= $model->attendance === \app\models\Rsvp::ATTENDANCE_ATTENDING ? 'bg-success' : 'bg-danger' ?>">
                                <i class="bi bi-<?= $model->attendance === \app\models\Rsvp::ATTENDANCE_ATTENDING ? 'check' : 'x' ?>-circle"></i>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">Status</small>
                            <div class="fw-bold"><?= $model->getAttendanceLabel() ?></div>
                        </div>
                    </div>

                    <?php if ($model->guests_count): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="me-3">
                                <div class="avatar-circle bg-info">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Total Tamu</small>
                                <div class="fw-bold"><?= $model->guests_count ?> Orang</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar-circle bg-warning">
                                <i class="bi bi-calendar"></i>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">Tanggal RSVP</small>
                            <div class="fw-bold"><?= date('d M Y', $model->created_at) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        Kontak
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-envelope me-1"></i>
                            Email
                        </small>
                        <a href="mailto:<?= Html::encode($model->email) ?>" class="text-decoration-none">
                            <?= Html::encode($model->email) ?>
                        </a>
                    </div>

                    <?php if ($model->phone): ?>
                        <div>
                            <small class="text-muted d-block mb-1">
                                <i class="bi bi-phone me-1"></i>
                                Telepon / WhatsApp
                            </small>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $model->phone) ?>" 
                               target="_blank" 
                               class="text-decoration-none">
                                <i class="bi bi-whatsapp me-1 text-success"></i>
                                <?= Html::encode($model->phone) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.message-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    position: relative;
}

.detail-view th {
    width: 200px;
    background: #f8f9fa;
    font-weight: 600;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
