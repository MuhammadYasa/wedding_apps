<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \app\models\Rsvp $rsvp */
/** @var \app\models\Invitation $invitation */

$this->title = 'Konfirmasi RSVP';
$attendanceText = $rsvp->attendance ? 'Hadir' : 'Tidak Hadir';
$attendanceColor = $rsvp->attendance ? '#28a745' : '#dc3545';
?>

<h2 style="color: #d4a574; font-weight: 300; margin-bottom: 10px;">Terima Kasih! 🎉</h2>

<p style="font-size: 16px; color: #666; margin-bottom: 30px;">
    Konfirmasi kehadiran Anda telah kami terima.
</p>

<div class="divider"></div>

<div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin: 25px 0; border-left: 4px solid <?= $attendanceColor ?>;">
    <h3 style="margin-top: 0; color: #333;">Detail Konfirmasi</h3>
    
    <p style="margin: 10px 0;">
        <strong>Nama:</strong> <?= Html::encode($rsvp->guest_name) ?>
    </p>
    
    <p style="margin: 10px 0;">
        <strong>Status Kehadiran:</strong> 
        <span style="color: <?= $attendanceColor ?>; font-weight: 600;">
            <?= $attendanceText ?>
        </span>
    </p>
    
    <?php if ($rsvp->attendance): ?>
        <p style="margin: 10px 0;">
            <strong>Jumlah Tamu:</strong> <?= Html::encode($rsvp->number_of_guests) ?> orang
        </p>
    <?php endif; ?>
    
    <?php if ($rsvp->message): ?>
        <p style="margin: 10px 0;">
            <strong>Pesan:</strong>
            <br>
            <em style="color: #666;">"<?= Html::encode($rsvp->message) ?>"</em>
        </p>
    <?php endif; ?>
    
    <p style="margin: 10px 0; font-size: 12px; color: #888;">
        <strong>Waktu Konfirmasi:</strong> <?= Yii::$app->formatter->asDatetime($rsvp->created_at) ?>
    </p>
</div>

<?php if ($rsvp->attendance): ?>
    <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 25px 0; border-left: 4px solid #0066cc;">
        <h4 style="margin-top: 0; color: #0066cc;">📅 Detail Acara</h4>
        <p style="margin: 8px 0;">
            <strong>Tanggal:</strong> <?= Yii::$app->formatter->asDate($invitation->event_date, 'long') ?>
        </p>
        <p style="margin: 8px 0;">
            <strong>Waktu:</strong> <?= Html::encode($invitation->event_time) ?>
        </p>
        <p style="margin: 8px 0;">
            <strong>Tempat:</strong> <?= Html::encode($invitation->venue) ?>
        </p>
        <p style="margin: 8px 0; color: #666; font-size: 14px;">
            <?= Html::encode($invitation->venue_address) ?>
        </p>
        
        <?php if ($invitation->venue_map_url): ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="<?= Html::encode($invitation->venue_map_url) ?>" 
                   style="display: inline-block; padding: 10px 25px; background: #0066cc; color: white !important; text-decoration: none; border-radius: 25px; font-size: 14px;">
                    📍 Lihat di Google Maps
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <p style="line-height: 1.8; margin: 25px 0;">
        Kami sangat menantikan kehadiran Anda! Mohon datang tepat waktu agar tidak 
        melewatkan momen berharga kami.
    </p>
<?php else: ?>
    <p style="line-height: 1.8; margin: 25px 0; color: #666;">
        Kami memahami Anda tidak dapat hadir. Terima kasih atas perhatian dan doa 
        yang telah Anda berikan. Semoga kita dapat bertemu di kesempatan lain.
    </p>
<?php endif; ?>

<div class="divider"></div>

<p style="font-size: 14px; color: #888; text-align: center; margin-top: 30px;">
    Jika ada perubahan, Anda dapat mengubah konfirmasi dengan mengakses kembali 
    halaman undangan.
</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?= Url::to(['invitation/view', 'slug' => $invitation->slug], true) ?>" class="btn">
        🎊 Lihat Undangan
    </a>
</div>

<p style="text-align: center; font-style: italic; color: #8b7355; margin-top: 30px; font-size: 14px;">
    Sampai jumpa di hari bahagia kami! ❤️
</p>
