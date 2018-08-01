<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use himiklab\colorbox\Colorbox;

$this->title = Yii::t('meter','Input of readings')." ".Yii::t('meter','for meter')." ".$passport['meterserialno'];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Address')." :"); ?> </div>
        <div class="col-md-7<?php echo " alert alert-info" ?>"> <?php echo Html::label( ($passport['addrstr']) ); ?> </div>

    </div>

<div class="panel panel-default">
  <div class="panel-heading"><?php echo Html::label(Yii::t('meter','Last readings')); ?></div>
  <div class="panel-body">
    <?php  if (!empty($LastReading)) {  ?>

        <?php //Метка времени ?>
        <div class="col-md-1"> <?php echo Html::label(Yii::t('meter','Date')." :"); ?> </div>
        <div class="col-md-2 alert alert-info"> <?php echo Html::label( $LastReading['mdatatime']  ); ?> </div>

        <?php //показания ?>
        <div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Readings')." :"); ?> </div>
        <div class="col-md-1 alert alert-info"> <?php echo Html::label( $LastReading['mdata']  ); ?> </div>

        <?php //ссылка на фотографию ?>
        <div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Photo')." :"); ?> </div>
        <div class="col-md-1 alert alert-info">
            <?php  if (!empty( $LastReading['mdatafile'])) { 
                echo "<a class='meterdataphoto' href=".Url::toRoute(['meter/get-meter-photo','MeterId' => $LastReading['mdatameter_id'], 'ReadingId'=>$LastReading['rec_id'], 'type'=>'.jpeg']).'><img src="/img/camera_small.png" alt="MDN"></a>'; //.' target="_blank"
            } ?>
        </div>

        <?php //Разделитель ?>
        <div class="col-md-1"> </div>

        <?php //Кнопка "Удалить все показания текущего периода" ?>
        <div class="col-md-1"> 
            <button class="btn btn-outline-primary btn-lg">
                <?php echo Html::a(
                    '<i class="glyphicon glyphicon-remove" style="color: red;"></i>',
                    Url::to(['delete-all-current', 'MeterId'=>$LastReading['mdatameter_id'],'ReadingId' => $LastReading['rec_id']]),
                    ['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')]
                ); ?>
            </button>
         </div>

    <?php } ?>

  </div>
</div>

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

<?= Colorbox::widget([
    'targets' => [
        '.meterdataphoto' => [
            'maxWidth' => 1000,
            'maxHeight' => 700,
            'opacity' => 0.6,
        ],
    ],
    'coreStyle' => 4
]) ?>
