<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Wish */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Edit Ucapan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kelola Ucapan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="wish-update">
    <h1><i class="bi bi-pencil"></i> <?= Html::encode($this->title) ?></h1>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Form Edit Ucapan</h5>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'is_approved')->checkbox([
                'label' => 'Setujui dan tampilkan ucapan ini',
                'labelOptions' => ['class' => 'form-check-label']
            ]) ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="bi bi-save"></i> Simpan', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="bi bi-arrow-left"></i> Batal', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
    border-radius: 10px;
}
.card-header {
    background: linear-gradient(135deg, #d4a574, #8b7355);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}
</style>
