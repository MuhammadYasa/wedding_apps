<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\QrCodeHelper;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $invitations app\models\Invitation[] */
/* @var $guests app\models\Guest[] */
/* @var $totalGuests int */
/* @var $checkedInGuests int */
/* @var $pendingGuests int */

$this->title = 'Guest Check-In';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="check-in-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-qr-code-scan"></i> <?= Html::encode($this->title) ?></h1>
        
        <?php if ($invitation): ?>
            <?= Html::beginForm(['bulk-generate', 'id' => $invitation->id], 'post', ['class' => 'd-inline']) ?>
                <?= Html::submitButton('<i class="bi bi-qr-code"></i> Generate QR Codes', [
                    'class' => 'btn btn-outline-primary',
                    'onclick' => 'return confirm("Generate QR codes for all guests without codes?")'
                ]) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>
    </div>

    <!-- Invitation Selector -->
    <?php if (Yii::$app->user->identity->isSuperUser() && count($invitations) > 1): ?>
        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label"><strong>Select Invitation:</strong></label>
                <select class="form-select" onchange="window.location.href='<?= Url::to(['index']) ?>?id=' + this.value">
                    <option value="">-- All Invitations --</option>
                    <?php foreach ($invitations as $inv): ?>
                        <option value="<?= $inv->id ?>" <?= $invitation && $invitation->id == $inv->id ? 'selected' : '' ?>>
                            <?= Html::encode($inv->title) ?> - <?= Yii::$app->formatter->asDate($inv->event_date) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h2 class="text-primary mb-0"><?= $totalGuests ?></h2>
                    <p class="text-muted mb-0">Total Guests</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h2 class="text-success mb-0"><?= $checkedInGuests ?></h2>
                    <p class="text-muted mb-0">Checked In</p>
                    <small class="text-muted"><?= $totalGuests > 0 ? round(($checkedInGuests / $totalGuests) * 100, 1) : 0 ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h2 class="text-warning mb-0"><?= $pendingGuests ?></h2>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Scanner Button -->
    <div class="text-center mb-4">
        <a href="<?= Url::to(['scanner']) ?>" class="btn btn-lg btn-primary">
            <i class="bi bi-camera"></i> Open QR Scanner
        </a>
    </div>

    <!-- Guest List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-people"></i> Guest List</h5>
        </div>
        <div class="card-body">
            <?php if (empty($guests)): ?>
                <p class="text-muted">No guests found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="guestTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>QR Code</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Checked In At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guests as $guest): ?>
                                <tr id="guest-<?= $guest->id ?>" class="<?= $guest->checked_in_at ? 'table-success' : '' ?>">
                                    <td><strong><?= Html::encode($guest->name) ?></strong></td>
                                    <td>
                                        <?php if ($guest->qr_code): ?>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    onclick="showQrCode(<?= $guest->id ?>, '<?= Html::encode($guest->name) ?>')">
                                                <i class="bi bi-qr-code"></i> View
                                            </button>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No QR</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?= $guest->email ? '<i class="bi bi-envelope"></i> ' . Html::encode($guest->email) . '<br>' : '' ?>
                                            <?= $guest->phone ? '<i class="bi bi-phone"></i> ' . Html::encode($guest->phone) : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($guest->checked_in_at): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Checked In
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $guest->checked_in_at ? Yii::$app->formatter->asDatetime($guest->checked_in_at) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($guest->checked_in_at): ?>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="checkOutGuest(<?= $guest->id ?>, '<?= Html::encode($guest->name) ?>')">
                                                <i class="bi bi-x-circle"></i> Check Out
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="checkInGuest(<?= $guest->id ?>, '<?= Html::encode($guest->name) ?>')">
                                                <i class="bi bi-check-circle"></i> Check In
                                            </button>
                                        <?php endif; ?>
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

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrCodeContainer">
                <!-- QR code will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function checkInGuest(guestId, guestName) {
    if (!confirm('Check in ' + guestName + '?')) return;
    
    $.ajax({
        url: '" . Url::to(['check-in']) . "',
        method: 'POST',
        data: {
            guest_id: guestId,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Failed to check in guest');
        }
    });
}

function checkOutGuest(guestId, guestName) {
    if (!confirm('Check out ' + guestName + '?')) return;
    
    $.ajax({
        url: '" . Url::to(['check-out']) . "',
        method: 'POST',
        data: {
            guest_id: guestId,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Failed to check out guest');
        }
    });
}

function showQrCode(guestId, guestName) {
    $('#qrCodeModalLabel').text('QR Code - ' + guestName);
    $('#qrCodeContainer').html('<div class="spinner-border text-primary"></div>');
    
    $.ajax({
        url: '" . Url::to(['get-qr-code']) . "',
        method: 'GET',
        data: { id: guestId },
        success: function(response) {
            if (response.success) {
                $('#qrCodeContainer').html('<img src="' + response.qr_code + '" class="img-fluid" style="max-width: 300px;">');
            } else {
                $('#qrCodeContainer').html('<p class="text-danger">Failed to load QR code</p>');
            }
        },
        error: function() {
            $('#qrCodeContainer').html('<p class="text-danger">Error loading QR code</p>');
        }
    });
    
    var modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    modal.show();
}
JS
);
?>

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
.table-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
</style>
