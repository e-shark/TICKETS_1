<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

$this->title = Yii::t('meter','Input of readings')." ".Yii::t('meter','for meter')." ".$passport['meterserialno'];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="panel panel-default">
  <div class="panel-heading"><?php echo Html::label(Yii::t('meter','Input of readings')); ?></div>
  <div class="panel-body">


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']],'post') ?>
    <?php echo Html::hiddenInput('MeterId', $passport['id']); ?>
    <?php echo Html::hiddenInput('RefUrl', $refurl); ?>

        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">Дата снятия показаний</span>
                <?php echo DatePicker::widget(['name'  => 'MeterDateTime', 
                                    'value'  => date("d-m-Y"),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]);
                ?>                
            </div>
        </div>

        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-addon">Показания</span>
            <?php echo Html::input('text','MeterData','',['id'=>'MeterData','class'=>'form-control']); ?> 
          </div>
        </div>


        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">Фото</span>
                <?php echo Html::input('file','imageFile','',['id'=>'imageFile','class'=>'form-control', 'accept'=>"image/*,image/jpeg"]); ?> 
            </div>
        </div>


        <div class="col-md-1">
            <div class="input-group">
           <?= Html::submitButton(Yii::t('app','Add'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['add-reading'])]) ?>
            </div>
        </div>


<?php ActiveForm::end() ?>

  </div>
</div>