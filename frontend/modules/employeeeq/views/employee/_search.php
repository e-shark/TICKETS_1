<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'remoteid') ?>

    <?= $form->field($model, 'firstname') ?>

    <?= $form->field($model, 'patronymic') ?>

    <?= $form->field($model, 'lastname') ?>

    <?php // echo $form->field($model, 'personcode') ?>

    <?php // echo $form->field($model, 'passportno') ?>

    <?php // echo $form->field($model, 'passportdata') ?>

    <?php // echo $form->field($model, 'personaddress') ?>

    <?php // echo $form->field($model, 'currentaddress') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <?php // echo $form->field($model, 'personphone') ?>

    <?php // echo $form->field($model, 'personphone1') ?>

    <?php // echo $form->field($model, 'personemail') ?>

    <?php // echo $form->field($model, 'personurl') ?>

    <?php // echo $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'married') ?>

    <?php // echo $form->field($model, 'education') ?>

    <?php // echo $form->field($model, 'employmentdate') ?>

    <?php // echo $form->field($model, 'dismissaldate') ?>

    <?php // echo $form->field($model, 'salary') ?>

    <?php // echo $form->field($model, 'rate') ?>

    <?php // echo $form->field($model, 'skillscategory') ?>

    <?php // echo $form->field($model, 'skillsrank') ?>

    <?php // echo $form->field($model, 'certprofessional') ?>

    <?php // echo $form->field($model, 'certmedical') ?>

    <?php // echo $form->field($model, 'certnarcology') ?>

    <?php // echo $form->field($model, 'certpsych') ?>

    <?php // echo $form->field($model, 'certcriminal') ?>

    <?php // echo $form->field($model, 'statusmilitary') ?>

    <?php // echo $form->field($model, 'statusdisability') ?>

    <?php // echo $form->field($model, 'statuschernobyl') ?>

    <?php // echo $form->field($model, 'lastjob') ?>

    <?php // echo $form->field($model, 'isemployed') ?>

    <?php // echo $form->field($model, 'employmenttype') ?>

    <?php // echo $form->field($model, 'oprights') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'occupation_id') ?>

    <?php // echo $form->field($model, 'division_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
