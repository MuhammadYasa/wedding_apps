<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \app\models\Rsvp $rsvp */
/** @var \app\models\Invitation $invitation */

$attendanceText = $rsvp->attendance ? 'HADIR' : 'TIDAK HADIR';
?>
KONFIRMASI RSVP - TERIMA KASIH!

Konfirmasi kehadiran Anda telah kami terima.

---

DETAIL KONFIRMASI

Nama: <?= $rsvp->guest_name ?>

Status: <?= $attendanceText ?>

<?php if ($rsvp->attendance): ?>
Jumlah Tamu: <?= $rsvp->number_of_guests ?> orang
<?php endif; ?>

<?php if ($rsvp->message): ?>
Pesan: "<?= $rsvp->message ?>"
<?php endif; ?>

Waktu Konfirmasi: <?= Yii::$app->formatter->asDatetime($rsvp->created_at) ?>


<?php if ($rsvp->attendance): ?>
DETAIL ACARA

Tanggal: <?= Yii::$app->formatter->asDate($invitation->event_date, 'long') ?>

Waktu: <?= $invitation->event_time ?>

Tempat: <?= $invitation->venue ?>

<?= $invitation->venue_address ?>

<?php if ($invitation->venue_map_url): ?>
Google Maps: <?= $invitation->venue_map_url ?>

<?php endif; ?>
Kami sangat menantikan kehadiran Anda! Mohon datang tepat waktu 
agar tidak melewatkan momen berharga kami.

<?php else: ?>
Kami memahami Anda tidak dapat hadir. Terima kasih atas perhatian 
dan doa yang telah Anda berikan.

<?php endif; ?>
---

Jika ada perubahan, Anda dapat mengubah konfirmasi dengan mengakses 
kembali halaman undangan.

Sampai jumpa di hari bahagia kami!
