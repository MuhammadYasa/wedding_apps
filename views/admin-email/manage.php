<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guests app\models\Guest[] */

$this->title = 'Manage Emails: ' . $invitation->title;
$this->params['breadcrumbs'][] = ['label' => 'Email Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$guestsWithEmail = array_filter($guests, function($g) { return !empty($g->email); });
$guestsWithoutEmail = array_filter($guests, function($g) { return empty($g->email); });
?>
<div class="admin-email-manage">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?= count($guests) ?></h3>
                    <p class="text-muted mb-0">Total Guests</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?= count($guestsWithEmail) ?></h3>
                    <p class="text-muted mb-0">With Email</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning"><?= count($guestsWithoutEmail) ?></h3>
                    <p class="text-muted mb-0">Without Email</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <?php if (count($guestsWithEmail) > 0): ?>
                        <?= Html::beginForm(['send-batch', 'id' => $invitation->id], 'post', ['onsubmit' => 'return confirm("Send emails to all ' . count($guestsWithEmail) . ' guests with email addresses?")']) ?>
                            <?= Html::submitButton('<i class="bi bi-send-fill"></i> Send All', ['class' => 'btn btn-success btn-sm']) ?>
                        <?= Html::endForm() ?>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-sm" disabled>No Emails</button>
                    <?php endif; ?>
                    <p class="text-muted mb-0 mt-2" style="font-size: 12px;">Batch Send</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Guests with Email -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-envelope-check"></i> 
                Guests with Email (<?= count($guestsWithEmail) ?>)
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($guestsWithEmail)): ?>
                <p class="text-muted">No guests with email addresses.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>RSVP Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guestsWithEmail as $guest): ?>
                                <?php 
                                $hasRsvp = \app\models\Rsvp::find()
                                    ->where(['invitation_id' => $invitation->id])
                                    ->andWhere(['or',
                                        ['email' => $guest->email],
                                        ['name' => $guest->name]
                                    ])
                                    ->exists();
                                ?>
                                <tr>
                                    <td><?= Html::encode($guest->name) ?></td>
                                    <td>
                                        <small><i class="bi bi-envelope"></i> <?= Html::encode($guest->email) ?></small>
                                    </td>
                                    <td>
                                        <small><?= Html::encode($guest->phone ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($hasRsvp): ?>
                                            <span class="badge bg-success">Responded</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?= Html::beginForm(['send-single', 'id' => $guest->id], 'post', ['style' => 'display:inline']) ?>
                                                <?= Html::submitButton('<i class="bi bi-send"></i> Send', ['class' => 'btn btn-primary btn-sm', 'onclick' => 'return confirm("Send invitation to ' . Html::encode($guest->name) . '?")']) ?>
                                            <?= Html::endForm() ?>
                                            
                                            <?php if (!$hasRsvp): ?>
                                                <?= Html::beginForm(['send-reminder', 'id' => $guest->id], 'post', ['style' => 'display:inline']) ?>
                                                    <?= Html::submitButton('<i class="bi bi-bell"></i> Remind', ['class' => 'btn btn-warning btn-sm', 'onclick' => 'return confirm("Send RSVP reminder to ' . Html::encode($guest->name) . '?")']) ?>
                                                <?= Html::endForm() ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Guests without Email -->
    <?php if (!empty($guestsWithoutEmail)): ?>
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Guests without Email (<?= count($guestsWithoutEmail) ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guestsWithoutEmail as $guest): ?>
                                <tr>
                                    <td><?= Html::encode($guest->name) ?></td>
                                    <td><?= Html::encode($guest->phone ?? '-') ?></td>
                                    <td>
                                        <small class="text-muted">Cannot send email</small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
    margin-bottom: 20px;
}
.card-header {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}
.card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107, #ff9800) !important;
}
</style>
