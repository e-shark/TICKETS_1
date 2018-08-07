<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app','Edit user');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app','Please, you can edit following fields:')?> </p>    

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-edituser']); ?>

                <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
