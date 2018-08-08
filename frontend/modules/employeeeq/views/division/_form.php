<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\DatePicker;
use frontend\modules\employeeeq\models\Division;

/* @var $this yii\web\View */
/* @var $model frontend\models\Division */
/* @var $form yii\widgets\ActiveForm */
$L=Лифты; $E=Электрощитовые; $S=Домофоны;
?>

<div class="division-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'divisioncode')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'divisionname')->textInput(['style'=>'width:300px', 'maxlength' => true])->hint('Обязательное к заполнению поле') ?>

    <?= $form->field($model, 'divisionfullname')->textInput(['style'=>'width:300px','maxlength' => true])->hint('Обязательное к заполнению поле') ?>

    <h4>
    <? $empl=$model->getDivisionemployeer();
    echo "Количество сотрудников: ", "$empl"; 
    //= $form->field($model, 'divisiondate')->textInput(['style'=>'width:300px']) ?>
    </h4> <br>

    <?= $form->field($model, 'divisioncodesvc')->radioList([ 'L'=>$L, 'E' =>$E,'S'=>$S, ''=>'Прочее']) 
        //'L'=>'Лифты', 'E' => 'Электричество','S'=>'Домофоны' 
    ?>    

    <?//$model->divisioncompany_id = 1;  //выставляем значение 1 ? >
    //< ?= $form->field($model, 'divisioncompany_id')->hiddenInput()->label(false) //скрытое поле
    //$form->field($model, 'divisioncompany_id')->textInput() //было
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        
        <?= Html::a('К Списку Подразделений', ['index'], ['class' => 'btn btn-primary']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
