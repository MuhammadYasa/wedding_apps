<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitations app\models\Invitation[] */

$this->title = 'Email Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-email-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <strong>Email Settings:</strong>
        <?php if (YII_ENV_DEV): ?>
            Development mode - Emails will be saved to <code>runtime/mail/</code> folder instead of being sent.
        <?php else: ?>
            Production mode - Emails will be sent via configured SMTP server.
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">📨 Invitations</h5>
        </div>
        <div class="card-body">
            <?php if (empty($invitations)): ?>
                <p class="text-muted">No invitations found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Couple</th>
                                <th>Event Date</th>
                                <th>Total Guests</th>
                                <th>With Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invitations as $invitation): ?>
                                <?php 
                                $totalGuests = count($invitation->guests);
                                $guestsWithEmail = count(array_filter($invitation->guests, function($g) {
                                    return !empty($g->email);
                                }));
                                ?>
                                <tr>
                                    <td><?= Html::encode($invitation->title) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= Html::encode($invitation->bride_nickname) ?> 
                                            & 
                                            <?= Html::encode($invitation->groom_nickname) ?>
                                        </small>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDate($invitation->event_date) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $totalGuests ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $guestsWithEmail > 0 ? 'success' : 'warning' ?>">
                                            <?= $guestsWithEmail ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['manage', 'id' => $invitation->id]) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-envelope"></i> Manage Emails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
}
.card-header {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}
</style>
