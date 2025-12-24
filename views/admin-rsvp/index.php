<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $stats array */
/* @var $invitations app\models\Invitation[] */
/* @var $currentAttendance string|null */
/* @var $currentInvitation int|null */

$this->title = 'Manajemen RSVP';
?>

<div class="admin-rsvp-index">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-calendar-check me-2"></i>
            <?= Html::encode($this->title) ?>
        </h1>
        <div>
            <?= Html::a(
                '<i class="bi bi-download me-2"></i>Export CSV',
                ['export', 'attendance' => $currentAttendance, 'invitation_id' => $currentInvitation],
                ['class' => 'btn btn-success']
            ) ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stats-card stats-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total RSVP</h6>
                            <h2 class="mb-0 fw-bold"><?= $stats['total'] ?></h2>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-envelope-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stats-card stats-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Hadir</h6>
                            <h2 class="mb-0 fw-bold"><?= $stats['attending'] ?></h2>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stats-card stats-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tidak Hadir</h6>
                            <h2 class="mb-0 fw-bold"><?= $stats['not_attending'] ?></h2>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stats-card stats-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Tamu</h6>
                            <h2 class="mb-0 fw-bold"><?= $stats['total_guests'] ?></h2>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="<?= Url::to(['index']) ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="attendance" class="form-label">
                        <i class="bi bi-funnel me-1"></i>
                        Filter Kehadiran
                    </label>
                    <select name="attendance" id="attendance" class="form-select">
                        <option value="">Semua</option>
                        <option value="attending" <?= $currentAttendance === 'attending' ? 'selected' : '' ?>>Hadir</option>
                        <option value="not_attending" <?= $currentAttendance === 'not_attending' ? 'selected' : '' ?>>Tidak Hadir</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="invitation_id" class="form-label">
                        <i class="bi bi-envelope me-1"></i>
                        Filter Undangan
                    </label>
                    <select name="invitation_id" id="invitation_id" class="form-select">
                        <option value="">Semua Undangan</option>
                        <?php foreach ($invitations as $invitation): ?>
                            <option value="<?= $invitation->id ?>" <?= $currentInvitation == $invitation->id ? 'selected' : '' ?>>
                                <?= Html::encode($invitation->title) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- RSVP Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover mb-0'],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width: 50px'],
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Nama',
                        'format' => 'html',
                        'value' => function ($model) {
                            return Html::a(
                                Html::encode($model->name),
                                ['view', 'id' => $model->id],
                                ['class' => 'text-decoration-none fw-bold']
                            );
                        },
                    ],
                    [
                        'attribute' => 'email',
                        'label' => 'Email',
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => 'Telepon',
                        'value' => function ($model) {
                            return $model->phone ?: '-';
                        },
                    ],
                    [
                        'attribute' => 'invitation_id',
                        'label' => 'Undangan',
                        'value' => function ($model) {
                            return $model->invitation ? $model->invitation->title : '-';
                        },
                    ],
                    [
                        'attribute' => 'attendance',
                        'label' => 'Kehadiran',
                        'format' => 'html',
                        'value' => function ($model) {
                            if ($model->attendance === \app\models\Rsvp::ATTENDANCE_ATTENDING) {
                                return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hadir</span>';
                            } else {
                                return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Tidak Hadir</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 120px'],
                    ],
                    [
                        'attribute' => 'guests_count',
                        'label' => 'Tamu',
                        'value' => function ($model) {
                            return $model->guests_count ?: '-';
                        },
                        'headerOptions' => ['style' => 'width: 80px; text-align: center'],
                        'contentOptions' => ['style' => 'text-align: center'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Tanggal',
                        'format' => ['date', 'php:d M Y H:i'],
                        'headerOptions' => ['style' => 'width: 150px'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a(
                                    '<i class="bi bi-eye"></i>',
                                    ['view', 'id' => $model->id],
                                    ['class' => 'btn btn-sm btn-info text-white me-1', 'title' => 'Lihat Detail']
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<i class="bi bi-trash"></i>',
                                    ['delete', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'Hapus',
                                        'data' => [
                                            'confirm' => 'Yakin ingin menghapus RSVP ini?',
                                            'method' => 'post',
                                        ],
                                    ]
                                );
                            },
                        ],
                        'headerOptions' => ['style' => 'width: 100px; text-align: center'],
                        'contentOptions' => ['style' => 'text-align: center'],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>

<style>
.stats-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.stats-primary .stats-icon {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.stats-success .stats-icon {
    background: rgba(72, 187, 120, 0.1);
    color: #48bb78;
}

.stats-danger .stats-icon {
    background: rgba(245, 101, 101, 0.1);
    color: #f56565;
}

.stats-info .stats-icon {
    background: rgba(66, 153, 225, 0.1);
    color: #4299e1;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.grid-view {
    overflow-x: auto;
}
</style>
