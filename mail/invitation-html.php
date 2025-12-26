<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \app\models\Invitation $invitation */
/** @var \app\models\Guest $guest */
/** @var string $invitationUrl */

$this->title = 'Undangan Pernikahan';
?>

<h2 style="color: #d4a574; font-weight: 300; margin-bottom: 10px;">Undangan Pernikahan</h2>

<p style="font-size: 18px; color: #8b7355; margin-bottom: 30px;">
    <strong><?= Html::encode($invitation->bride_name) ?></strong> 
    <span style="font-size: 24px;">&</span> 
    <strong><?= Html::encode($invitation->groom_name) ?></strong>
</p>

<div class="divider"></div>

<p>Kepada Yth,</p>
<h3 style="color: #333; margin: 10px 0 20px;">
    <?= Html::encode($guest->name) ?>
</h3>
<p style="color: #666; margin-bottom: 30px;">Di Tempat</p>

<p style="line-height: 1.8;">
    Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang 
    Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami yang akan 
    dilaksanakan pada:
</p>

<div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin: 25px 0; border-left: 4px solid #d4a574;">
    <p style="margin: 8px 0;">
        <strong style="color: #d4a574;">📅 Tanggal:</strong> 
        <?= Yii::$app->formatter->asDate($invitation->event_date, 'long') ?>
    </p>
    <p style="margin: 8px 0;">
        <strong style="color: #d4a574;">⏰ Waktu:</strong> 
        <?= Html::encode($invitation->event_time) ?>
    </p>
    <p style="margin: 8px 0;">
        <strong style="color: #d4a574;">📍 Tempat:</strong> 
        <?= Html::encode($invitation->venue) ?>
    </p>
    <p style="margin: 8px 0; color: #666;">
        <?= Html::encode($invitation->venue_address) ?>
    </p>
</div>

<p style="line-height: 1.8; margin-bottom: 30px;">
    Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila 
    Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu 
    kepada kami.
</p>

<div style="text-align: center; margin: 40px 0;">
    <a href="<?= $invitationUrl ?>" class="btn">
        🎊 Buka Undangan
    </a>
</div>

<p style="font-size: 14px; color: #888; text-align: center; margin-top: 30px;">
    Atas kehadiran dan doa restunya, kami ucapkan terima kasih.
</p>

<div class="divider"></div>

<p style="text-align: center; font-style: italic; color: #8b7355; margin-top: 30px;">
    "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan 
    untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya"
    <br><strong>(QS. Ar-Rum: 21)</strong>
</p>
