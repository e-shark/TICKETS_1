<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ElevatorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="elevator-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'elremoteid') ?>

    <?= $form->field($model, 'eldevicetype') ?>

    <?= $form->field($model, 'elserialno') ?>

    <?= $form->field($model, 'elmodel') ?>

    <?php // echo $form->field($model, 'eldate') ?>

    <?php // echo $form->field($model, 'elload') ?>

    <?php // echo $form->field($model, 'elspeed') ?>

    <?php // echo $form->field($model, 'elstops') ?>

    <?php // echo $form->field($model, 'eldoortype') ?>

    <?php // echo $form->field($model, 'eltype') ?>

    <?php // echo $form->field($model, 'elporchno') ?>

    <?php // echo $form->field($model, 'elporchpos') ?>

    <?php // echo $form->field($model, 'elinventoryno') ?>

    <?php // echo $form->field($model, 'elregyear') ?>

    <?php // echo $form->field($model, 'elrtu_id') ?>

    <?php // echo $form->field($model, 'elfacility_id') ?>

    <?php // echo $form->field($model, 'eldivision_id') ?>

    <?php // echo $form->field($model, 'elperson_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
