<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Occupation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="occupation-form">

    <?php $form = ActiveForm::begin(); ?>

    	<?= $form->field($model, 'occupationname')->textInput(['style'=>'width:400px', 'maxlength' => true]) ?>

 		<?= $form->field($model, 'occupationcode', [
            'template' => '<div>
                        <label><span>Код Должности</span>{input}</label>{error}</div>',

        ])->textInput(['maxlength' => true]) ?>
        <h4>
        <? $empl=$model->getOccupationemployeer();
           echo "Человек на должности: ", "$empl";
        ?> </h4>
    <div class="form-group"> 
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::a('К Списку Должностей', ['index'], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
