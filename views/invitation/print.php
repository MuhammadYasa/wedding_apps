<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guest app\models\Guest|null */

$this->title = $invitation->title . ' - Printable';

// Register print-specific CSS
$this->registerCss("
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    .no-print {
        display: none !important;
    }
    .printable-invitation {
        page-break-inside: avoid;
    }
    @page {
        margin: 2cm;
        size: A4 portrait;
    }
}

@media screen {
    .printable-invitation {
        max-width: 21cm;
        min-height: 29.7cm;
        padding: 2cm;
        margin: 20px auto;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
}

.printable-invitation {
    font-family: 'Times New Roman', serif;
    line-height: 1.6;
}

.print-header {
    text-align: center;
    margin-bottom: 40px;
    border-bottom: 3px double #d4a574;
    padding-bottom: 20px;
}

.print-header h1 {
    font-size: 32pt;
    margin-bottom: 10px;
    color: #8b7355;
}

.print-section {
    margin: 30px 0;
    page-break-inside: avoid;
}

.print-section h2 {
    font-size: 18pt;
    border-bottom: 2px solid #d4a574;
    padding-bottom: 10px;
    margin-bottom: 15px;
    color: #8b7355;
}

.couple-names {
    font-size: 24pt;
    text-align: center;
    margin: 20px 0;
    font-weight: bold;
    color: #8b7355;
}

.event-details {
    background: #f9f9f9;
    padding: 20px;
    border-left: 4px solid #d4a574;
    margin: 20px 0;
}

.event-details p {
    margin: 10px 0;
    font-size: 12pt;
}

.qr-code {
    text-align: center;
    margin: 30px 0;
}

.qr-code img {
    width: 200px;
    height: 200px;
    border: 2px solid #d4a574;
    padding: 10px;
}

.footer-print {
    text-align: center;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 2px solid #d4a574;
    font-style: italic;
}
");
?>

<div class="no-print" style="text-align: center; padding: 20px; background: #f5f5f5;">
    <button onclick="window.print()" class="btn btn-primary btn-lg">
        <i class="bi bi-printer"></i> Print Undangan
    </button>
    <?= Html::a('<i class="bi bi-arrow-left"></i> Kembali ke Undangan', 
        ['view', 'slug' => $invitation->slug], 
        ['class' => 'btn btn-secondary btn-lg']) ?>
</div>

<div class="printable-invitation">
    <!-- Header -->
    <div class="print-header">
        <h1>Wedding Invitation</h1>
        <p style="font-size: 14pt; color: #666;">
            Undangan Pernikahan
        </p>
    </div>

    <!-- Couple Names -->
    <div class="couple-names">
        <?= Html::encode($invitation->bride_name) ?>
        <div style="font-size: 18pt; margin: 10px 0;">&</div>
        <?= Html::encode($invitation->groom_name) ?>
    </div>

    <!-- Event Details -->
    <div class="print-section">
        <h2>Detail Acara</h2>
        <div class="event-details">
            <p><strong>Tanggal:</strong> <?= Yii::$app->formatter->asDate($invitation->event_date, 'php:l, d F Y') ?></p>
            <p><strong>Waktu:</strong> <?= Html::encode($invitation->event_time) ?> WIB</p>
            <p><strong>Tempat:</strong> <?= Html::encode($invitation->venue) ?></p>
            <?php if ($invitation->venue_address): ?>
                <p><strong>Alamat:</strong> <?= nl2br(Html::encode($invitation->venue_address)) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Story -->
    <?php if ($invitation->story): ?>
        <div class="print-section">
            <h2>Kisah Kami</h2>
            <div style="text-align: justify;">
                <?= nl2br(Html::encode($invitation->story)) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Maps Link -->
    <?php if ($invitation->venue_map_url): ?>
        <div class="print-section">
            <h2>Lokasi</h2>
            <p><strong>Google Maps:</strong></p>
            <p style="word-break: break-all; font-size: 10pt;">
                <?= Html::encode($invitation->venue_map_url) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- QR Code for Digital Access -->
    <div class="qr-code no-print">
        <h2 style="font-size: 14pt; color: #8b7355;">Scan untuk Akses Digital</h2>
        <div id="qrcode"></div>
        <p style="font-size: 10pt; color: #666; margin-top: 10px;">
            Scan QR code di atas untuk membuka undangan digital
        </p>
    </div>

    <!-- Footer -->
    <div class="footer-print">
        <p>Merupakan suatu kehormatan dan kebahagiaan bagi kami</p>
        <p>apabila Bapak/Ibu/Saudara/i berkenan hadir</p>
        <p>dan memberikan doa restu kepada kami.</p>
        <p style="margin-top: 20px; font-weight: bold;">
            Terima kasih atas perhatian dan doa restunya.
        </p>
    </div>

    <!-- Print Info -->
    <div style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-radius: 5px; font-size: 10pt;">
        <p><strong>Undangan Digital:</strong> <?= Url::to(['invitation/view', 'slug' => $invitation->slug], true) ?></p>
        <p><strong>Dicetak pada:</strong> <?= date('d F Y, H:i') ?> WIB</p>
    </div>
</div>

<?php
// Add QR Code generation - link to thank you page
$thankyouUrl = Url::to(['invitation/thankyou', 'slug' => $invitation->slug, 'to' => $guest->name ?? 'Tamu'], true);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJs("
    new QRCode(document.getElementById('qrcode'), {
        text: '{$thankyouUrl}',
        width: 200,
        height: 200,
        colorDark: '#8b7355',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
", \yii\web\View::POS_READY);
?>
