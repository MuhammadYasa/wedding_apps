<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $wish app\models\Wish */
/* @var $invitation app\models\Invitation */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Tulis Ucapan - ' . $invitation->title;
$this->params['breadcrumbs'][] = ['label' => 'Undangan', 'url' => ['invitation/view', 'slug' => $invitation->slug]];
$this->params['breadcrumbs'][] = ['label' => 'Buku Tamu', 'url' => ['index', 'id' => $invitation->id]];
$this->params['breadcrumbs'][] = 'Tulis Ucapan';
?>

<div class="wish-create">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #d4a574, #8b7355); border-radius: 15px 15px 0 0 !important;">
                    <h2 class="mb-0">
                        <i class="bi bi-pen-fill me-2"></i> Tulis Ucapan
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Untuk: <?= Html::encode($invitation->title) ?></p>
                </div>
                
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Tips:</strong> Tuliskan ucapan selamat, doa terbaik, atau kenangan indah Anda dengan pengantin.
                    </div>

                    <?php $form = ActiveForm::begin([
                        'id' => 'wish-form',
                        'options' => ['class' => 'wish-form'],
                    ]); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($wish, 'name')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'Masukkan nama Anda',
                                'class' => 'form-control form-control-lg'
                            ])->label('<i class="bi bi-person"></i> Nama Anda *') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($wish, 'email')->textInput([
                                'maxlength' => true,
                                'placeholder' => 'email@example.com (opsional)',
                                'class' => 'form-control form-control-lg'
                            ])->label('<i class="bi bi-envelope"></i> Email (Opsional)') ?>
                        </div>
                    </div>

                    <?= $form->field($wish, 'message')->textarea([
                        'rows' => 6,
                        'placeholder' => 'Tuliskan ucapan selamat, doa terbaik, atau pesan Anda di sini...',
                        'class' => 'form-control form-control-lg',
                        'maxlength' => 1000,
                        'id' => 'message-input'
                    ])->label('<i class="bi bi-chat-heart"></i> Pesan & Ucapan *') ?>

                    <div class="text-end">
                        <small class="text-muted">
                            <span id="char-count">0</span> / 1000 karakter
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <?= Html::submitButton('<i class="bi bi-send-fill me-2"></i> Kirim Ucapan', [
                            'class' => 'btn btn-primary btn-lg',
                            'id' => 'submit-btn'
                        ]) ?>
                        
                        <?= Html::a('<i class="bi bi-arrow-left me-2"></i> Kembali', 
                            ['index', 'id' => $invitation->id], 
                            ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Preview Recent Wishes -->
            <div class="mt-5">
                <h4 class="mb-3"><i class="bi bi-eye"></i> Ucapan Terbaru</h4>
                <?php
                $recentWishes = \app\models\Wish::getApprovedWishes($invitation->id, 3);
                if (!empty($recentWishes)):
                ?>
                    <div class="row g-3">
                        <?php foreach ($recentWishes as $recentWish): ?>
                            <div class="col-md-12">
                                <?= $this->render('_wish_card', ['model' => $recentWish]) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada ucapan yang ditampilkan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
// Character counter
$('#message-input').on('input', function() {
    var count = $(this).val().length;
    $('#char-count').text(count);
    
    if (count > 1000) {
        $('#char-count').addClass('text-danger').removeClass('text-muted');
    } else {
        $('#char-count').addClass('text-muted').removeClass('text-danger');
    }
});

// Submit button loading state
$('#wish-form').on('beforeSubmit', function() {
    $('#submit-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...');
});
JS
);
?>

<style>
.wish-form label {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
}

.wish-form .form-control:focus {
    border-color: #d4a574;
    box-shadow: 0 0 0 0.25rem rgba(212, 165, 116, 0.25);
}

.card {
    border-radius: 15px !important;
}

.btn-primary {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    border: none;
    transition: transform 0.2s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
</style>
