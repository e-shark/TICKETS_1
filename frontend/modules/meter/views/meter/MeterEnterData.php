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

        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-addon">Дата </span>
                <?php echo DatePicker::widget(['name'  => 'MeterDate', 
                                    'value'  => date("d-m-Y"),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]);
                ?>                
            </div>
        </div>

        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-addon">Время </span>
                    <?php //$ts = ["00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00"]; ?>
                    <?php $ts=""; for($i=0;$i<24;$i++) $ts[]=sprintf( "%02d:00", $i); ?>
                    <?php echo Html::dropDownList('MeterTime', date("H"), $ts, ['id'=>'MeterTime','class'=>'form-control','onChange'=>'onSelectRegion()']); ?> 
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