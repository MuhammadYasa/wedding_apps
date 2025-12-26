<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Wish */
/* @var $invitation app\models\Invitation */
?>

<div class="wish-card">
    <div class="card h-100 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-start mb-3">
                <div class="avatar-circle me-3">
                    <?= strtoupper(mb_substr($model->name, 0, 1)) ?>
                </div>
                <div class="flex-grow-1">
                    <h5 class="card-title mb-1">
                        <?= Html::encode($model->name) ?>
                    </h5>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> <?= $model->getFormattedDate() ?>
                    </small>
                </div>
            </div>
            
            <div class="card-text wish-message">
                <i class="bi bi-quote text-muted"></i>
                <p class="mb-0 mt-2"><?= nl2br(Html::encode($model->message)) ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.wish-card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.wish-card .card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.wish-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.25rem;
}

.wish-message {
    font-style: italic;
    color: #555;
    line-height: 1.6;
}

.wish-message .bi-quote {
    font-size: 2rem;
    opacity: 0.3;
}
</style>
