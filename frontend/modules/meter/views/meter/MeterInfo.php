<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use himiklab\colorbox\Colorbox;
use yii\jui\DatePicker;

$this->title = Yii::t('meter','Meter passport')." ".$passport['meterserialno'];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (!empty($passport)) {                  // Проверяем, а существует ли указанный счетчик ?>
<h1><?= Html::encode($this->title) ?></h1>
<div>
	<?php $FieldType=" alert alert-info" ?>
	<?php //$FieldType=" label label-info" ?>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Type')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metermodel']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Serial №')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterserialno']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Phases')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterphases']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Digits')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterdigits']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Current')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metercurrent']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Current max')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metermaxcurrent']) ); ?> </div>
    </div>
    <div class="row">
        <div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Voltage')." :"); ?> </div>
        <div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metervoltage']) ); ?> </div>
        <div class="col-md-1"></div>
        <div class="col-md-2"> <?php echo Html::label(Yii::t('meter','System №')." :"); ?> </div>
        <div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metersysno']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Comm №')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metercomno']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','IMEI')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterimei']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Phone')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterphone']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','IP')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterip']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Calibr. period')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metecalibrationinterval']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Calibr. data')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['metecalibrationdata']) ); ?> </div>  <?php // берем из таблици показаний а не в паспорте ?>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Owner')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterowner']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Inventory №')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meterinventoryno']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Account')." :"); ?> </div>
    	<div class="col-md-2<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meteraccno']) ); ?> </div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Account name')." :"); ?> </div>
    	<div class="col-md-5<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['meteraccname']) ); ?> </div>
    </div>
    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Address')." :"); ?> </div>
    	<div class="col-md-7<?php echo $FieldType ?>"> <?php echo Html::label( ($passport['addrstr']) ); ?> </div>
    </div>

    <?php echo Html::a(Yii::t('meter','Edit'), Url::toRoute(['meter/meter-edit','MeterId'=>$passport['id']]), ['class' =>'submit btn btn-success']); ?>

</div>

<a id="meterdata"></a>      <?php // Метка, чтобы можно было сюда страницу позиционировать после ввода показаний ?>
<div>
	<H2> <?php echo Yii::t('meter','Meter readings'); ?> </H2>
	<?php  
    $dataColumns = [
        //['class' => 'yii\grid\SerialColumn'],
        [
            'label' =>Yii::t('meter','Date'),
            //'attribute' => 'mdatatime',
            'content' => function($data){
            	return "<p data-toggle='tooltip' data-placement='top' title='{$data['employee']}'>{$data['mdatatime']}</p>";
            }
        ],
        [
            'label' =>Yii::t('meter','Readings'),
            'attribute' => 'mdata',
        ],
        [
            'label' => Yii::t('meter','State'),
            'content' => function($data){
            	$res="";
            	if ($data['mdatameterstate'] == '1') $res = "Ok";
            	return $res;
            }
        ],
        [
        	'label' => Yii::t('meter','Photo'),
        	'content' => function($data){
				if (!empty( $data['mdatafile']))
					//$res = "<a class='meterdataphoto' href=".Url::base()."/ReadingsPhoto/M".$data['mdatameter_id']."/R".$data['id']."/1.8.0.jpg".'><img src="/img/camera_small.png" alt="MDN"></a>'; //.' target="_blank"
					$res = "<a class='meterdataphoto' href=".Url::toRoute(['meter/get-meter-photo','MeterId' => $data['mdatameter_id'], 'ReadingId'=>$data['id'], 'type'=>'.jpeg']).'><img src="/img/camera_small.png" alt="MDN"></a>'; //.' target="_blank"
				else
					$res = "";
				return $res;
         	}
		],
    ];
    // Разрешенным пользователям добавляем возможность удалять записи
    // ! ! !   Тут надо добавить проверку пользователя   ! ! !

	array_push( $dataColumns, 
		[
			//'class' => 'yii\grid\ActionColumn', 
			//'template' => '{deletereading}',
            'content' => function($model) {
                    return Html::a(
                        //'<span class="glyphicon glyphicon-trash" style="color: red;">',
                        '<span class="glyphicon glyphicon-remove" style="color: red;"></span>',
                        Url::to(['delete-reading', 'MeterId'=>$model['mdatameter_id'],'ReadingId' => $model['id']]),
                        ['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')]
                    );
                }		
        ]
	);
    /* 	или так, через ActionColumn
	array_push( $dataColumns, 
		[
			'class' => 'yii\grid\ActionColumn', 
			'template' => '{deletereading}',
            'buttons' => [
                'deletereading' => function($url, $model, $key) {
                    return Html::a(
                        //'<span class="glyphicon glyphicon-trash">',
                        '<span class="glyphicon glyphicon-remove" style="color: red;">',
                        Url::to(['delete-reading', 'MeterId'=>$model['mdatameter_id'],'ReadingId' => $model['id']]),
                        ['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')]
                    );
                }		
            ],
        ]
	);*/

	echo GridView::widget([
		'dataProvider' => $meterdata,
		'columns' => $dataColumns, 
	]); ?> 

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


<div class="panel panel-default">
  <div class="panel-heading"><?php echo Html::label(Yii::t('meter','Input of readings')); ?></div>
  <div class="panel-body">


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']],'post') ?>
    <?php echo Html::hiddenInput('MeterId', $passport['id']); ?>

        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-addon">Дата</span>
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
                    <?php $ts=""; for($i=0;$i<24;$i++) $ts[]=sprintf( "%02d:00", $i); ?>
                    <?php echo Html::dropDownList('MeterTime', date("H"), $ts, ['id'=>'MeterTime','class'=>'form-control','onChange'=>'onSelectRegion()']); ?> 
            </div>
        </div>

        <!--div class="row panel panel-info"-->
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

<?php }else{ // Если указан несуществующий ID счетчика ?>
<div>
    <h1><?= Yii::t('meter','Information on the meter not found') ?></h1>
    <a href="<?= Yii::$app->request->referrer ?>" class="btn btn-primary"><?=Yii::t('meter','Back')?></a>
</div>
<?php } ?>
