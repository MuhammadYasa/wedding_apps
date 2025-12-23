<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Gallery */
/* @var $invitation app\models\Invitation */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Edit Foto Gallery';
$this->params['breadcrumbs'][] = ['label' => 'Undangan', 'url' => ['admin-invitation/index']];
$this->params['breadcrumbs'][] = ['label' => $invitation->title, 'url' => ['admin-invitation/view', 'id' => $invitation->id]];
$this->params['breadcrumbs'][] = ['label' => 'Gallery', 'url' => ['index', 'invitation_id' => $invitation->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="admin-gallery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="gallery-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Jika Anda mengunggah gambar baru, gambar lama akan terhapus secara otomatis.
                        </div>

                        <?= $form->field($model, 'imageFile')->fileInput([
                            'accept' => 'image/*',
                            'class' => 'form-control'
                        ])->label('Ganti Gambar (Opsional)')->hint('Kosongkan jika tidak ingin mengganti gambar') ?>

                        <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'sort_order')->textInput(['type' => 'number']) ?>

                        <div class="form-group">
                            <?= Html::submitButton('<i class="bi bi-save"></i> Update', ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('<i class="bi bi-arrow-left"></i> Kembali', ['index', 'invitation_id' => $invitation->id], ['class' => 'btn btn-secondary']) ?>
                            <?= Html::a('<i class="bi bi-trash"></i> Hapus', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Apakah Anda yakin ingin menghapus foto ini?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <strong>Gambar Saat Ini</strong>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= $model->getImageUrl() ?>" class="img-fluid rounded mb-3" alt="Current Image">
                        <p class="text-muted small">
                            <i class="bi bi-info-circle"></i> 
                            Ukuran file: <?php
                                $filePath = Yii::getAlias('@webroot/uploads/invitations/' . $model->invitation_id . '/gallery/' . $model->filename);
                                if (file_exists($filePath)) {
                                    echo number_format(filesize($filePath) / 1024, 2) . ' KB';
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <strong>Preview Gambar Baru</strong>
                    </div>
                    <div class="card-body text-center">
                        <img id="image-preview" src="<?= $model->getImageUrl() ?>" class="img-fluid rounded" alt="Preview">
                        <p class="text-muted small mt-2">Preview akan berubah setelah memilih gambar baru</p>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php
// Image preview script
$this->registerJs("
$('input[type=\"file\"]').on('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#image-preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    }
});
", \yii\web\View::POS_READY);
?>
