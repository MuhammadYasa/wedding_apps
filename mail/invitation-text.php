<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \app\models\Invitation $invitation */
/** @var \app\models\Guest $guest */
/** @var string $invitationUrl */
?>
UNDANGAN PERNIKAHAN

<?= $invitation->bride_name ?> & <?= $invitation->groom_name ?>

---

Kepada Yth,
<?= $guest->name ?>

Di Tempat

Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang 
Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami yang akan 
dilaksanakan pada:

Tanggal: <?= Yii::$app->formatter->asDate($invitation->event_date, 'long') ?>

Waktu: <?= $invitation->event_time ?>

Tempat: <?= $invitation->venue ?>

<?= $invitation->venue_address ?>


Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila 
Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kami.

Buka undangan lengkap di: <?= $invitationUrl ?>


Atas kehadiran dan doa restunya, kami ucapkan terima kasih.

---

"Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan 
untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya"
(QS. Ar-Rum: 21)
