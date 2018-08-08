<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\modules\employeeeq\models\Division;
?>

<div class="elevator-form">

    <?php $form = ActiveForm::begin(['id'=>'formarrr', 'action'=>['senddivision'], 'method'=>"post"]); ?>

     <?=  $form->field($model, 'id',// П О Д Р А З Д Е Л Е Н И Е
        [
            'template' => '<div class=col-md-3> 
            <label> <span>Подразделение</span>{input}</label>{error}</div>',
        ]
        )->dropDownList( 
            ArrayHelper::map(
                Division::find()->all(), 'id', 'divisionname'), 
            [   'name'=>'name',
                'prompt'=>'Все',
                'onchange'=>'this.form.submit()'
            ]
        ) ?>
        <br> 
        <?//= Html::submitButton('Применить', ['class' => 'btn btn-success']) ?>
        <br>  <br> 
    <?php ActiveForm::end(); ?>
</div>
