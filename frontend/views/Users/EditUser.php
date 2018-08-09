<?php

use yii\helpers\Url;
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
                <?= Html::hiddenInput('firstref', $model->firstref); ?>
                <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php 
    // кнопка "Удалить пользователя"
    echo Html::a(
        '<span class="glyphicon glyphicon-remove" style="color: red;"></span>'."&nbsp".Yii::t('app','Delete user'),
        Url::to(['delete-user', 'UserID'=>$data['id']]),
        ['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),'class'=>'btn btn-primary']
    );
?>

<?php 
    // кнопка "Вернуться"
    if (empty($model->firstref)) echo Html::a(Yii::t('app','Back'), Url::toRoute(['users/index']), ['class'=>'btn btn-primary']);
    else echo Html::a(Yii::t('app','Back'), urldecode($model->firstref), ['class'=>'btn btn-primary']);
?>
