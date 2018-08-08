<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Elevator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="elevator-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'elremoteid')->textInput() ?>

    <?= $form->field($model, 'eldevicetype')->textInput() ?>

    <?= $form->field($model, 'elserialno')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elmodel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eldate')->textInput() ?>

    <?= $form->field($model, 'elload')->textInput() ?>

    <?= $form->field($model, 'elspeed')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elstops')->textInput() ?>

    <?= $form->field($model, 'eldoortype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'eltype')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elporchno')->textInput() ?>

    <?= $form->field($model, 'elporchpos')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elinventoryno')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elregyear')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'elrtu_id')->textInput() ?>

    <?= $form->field($model, 'elfacility_id')->textInput() ?>

    <?= $form->field($model, 'eldivision_id')->textInput() ?>

    <?= $form->field($model, 'elperson_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
