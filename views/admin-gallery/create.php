<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Gallery */
/* @var $invitation app\models\Invitation */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Tambah Foto Gallery';
$this->params['breadcrumbs'][] = ['label' => 'Undangan', 'url' => ['admin-invitation/index']];
$this->params['breadcrumbs'][] = ['label' => $invitation->title, 'url' => ['admin-invitation/view', 'id' => $invitation->id]];
$this->params['breadcrumbs'][] = ['label' => 'Gallery', 'url' => ['index', 'invitation_id' => $invitation->id]];
$this->params['breadcrumbs'][] = 'Tambah';

// Register gallery preview script
$this->registerJsFile('@web/js/gallery-preview.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<div class="admin-gallery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="gallery-form">

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?= $form->field($model, 'imageFile[]')->fileInput([
                            'accept' => 'image/*',
                            'class' => 'form-control',
                            'multiple' => true
                        ])->label('Pilih Gambar <span class="text-danger">*</span>')->hint('Format: JPG, JPEG, PNG. Maksimal 5MB per foto. Bisa pilih beberapa foto sekaligus (max 20 foto)') ?>

                        <?= $form->field($model, 'caption')->textInput(['maxlength' => true])->hint('Opsional, caption ini akan digunakan untuk semua foto yang diupload') ?>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Tips Upload Multiple:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Bisa upload <strong>maksimal 20 foto sekaligus</strong></li>
                                <li>Tekan <kbd>Ctrl</kbd> (Windows) atau <kbd>Cmd</kbd> (Mac) untuk pilih beberapa foto</li>
                                <li>Ukuran gambar yang disarankan: 1200 x 800 px</li>
                                <li>Format yang didukung: JPG, JPEG, PNG</li>
                                <li>Ukuran maksimal: 5 MB per foto</li>
                                <li>Sistem akan otomatis membuat thumbnail 300x300 px untuk setiap foto</li>
                                <li>Urutan foto akan otomatis diatur, bisa diubah nanti dengan drag & drop</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <?= Html::submitButton('<i class="bi bi-save"></i> Simpan', ['class' => 'btn btn-success']) ?>
                            <?= Html::a('<i class="bi bi-arrow-left"></i> Kembali', ['index', 'invitation_id' => $invitation->id], ['class' => 'btn btn-secondary']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <strong>Preview</strong>
                    </div>
                    <div class="card-body">
                        <div id="preview-container" style="max-height: 500px; overflow-y: auto;">
                            <p class="text-center text-muted small">
                                <i class="bi bi-images"></i><br>
                                Preview akan muncul setelah memilih gambar
                            </p>
                        </div>
                        <div id="file-count" class="text-center mt-2 text-muted small"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
