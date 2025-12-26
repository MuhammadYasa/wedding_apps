<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $invitation app\models\Invitation */
/* @var $guestName string|null */

$this->title = 'Terima Kasih - ' . $invitation->title;
?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Georgia', serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thankyou-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        padding: 60px 40px;
        text-align: center;
        max-width: 600px;
        margin: 20px;
        animation: fadeInScale 0.6s ease-out;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .thankyou-icon {
        font-size: 80px;
        color: #28a745;
        margin-bottom: 20px;
        animation: bounceIn 0.8s ease-out;
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    .thankyou-title {
        font-size: 42px;
        color: #333;
        margin: 20px 0;
        font-weight: 300;
        letter-spacing: 2px;
    }

    .guest-name {
        font-size: 32px;
        color: #667eea;
        font-weight: bold;
        margin: 20px 0;
        text-transform: capitalize;
    }

    .thankyou-message {
        font-size: 20px;
        color: #666;
        margin: 20px 0;
        line-height: 1.8;
    }

    .couple-names {
        font-size: 36px;
        color: #764ba2;
        margin: 30px 0;
        font-weight: bold;
    }

    .ampersand {
        font-size: 28px;
        color: #d4a574;
        margin: 0 15px;
        font-style: italic;
    }

    .divider {
        width: 100px;
        height: 3px;
        background: linear-gradient(to right, #667eea, #764ba2);
        margin: 30px auto;
        border-radius: 2px;
    }

    .decorative-element {
        color: #d4a574;
        font-size: 24px;
        margin: 20px 0;
    }

    @media (max-width: 768px) {
        .thankyou-container {
            padding: 40px 30px;
        }

        .thankyou-title {
            font-size: 32px;
        }

        .guest-name {
            font-size: 26px;
        }

        .couple-names {
            font-size: 28px;
        }

        .thankyou-message {
            font-size: 18px;
        }
    }
</style>

<div class="thankyou-container">
    <!-- Success Icon -->
    <div class="thankyou-icon">
        <i class="bi bi-check-circle-fill"></i>
    </div>

    <!-- Thank You Title -->
    <h1 class="thankyou-title">Terima Kasih</h1>

    <!-- Guest Name -->
    <?php if ($guestName): ?>
        <div class="guest-name">
            <?= Html::encode($guestName) ?>
        </div>
    <?php endif; ?>

    <div class="divider"></div>

    <!-- Message -->
    <p class="thankyou-message">
        Telah datang ke pernikahan
    </p>

    <!-- Decorative Element -->
    <div class="decorative-element">
        ❦
    </div>

    <!-- Couple Names -->
    <div class="couple-names">
        <?= Html::encode($invitation->bride_name) ?>
        <span class="ampersand">&</span>
        <?= Html::encode($invitation->groom_name) ?>
    </div>

    <div class="divider"></div>

    <!-- Additional Message -->
    <p class="thankyou-message" style="font-size: 16px; margin-top: 30px;">
        Kehadiran dan doa Anda sangat berarti bagi kami
    </p>
</div>
