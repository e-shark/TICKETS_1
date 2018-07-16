<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

$IsNewMeter = empty($model->MeterId);
if ($IsNewMeter)
	$this->title = Yii::t('meter','Add new meter');
else
	$this->title = Yii::t('meter','Edit meter passport');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title.' '.$passport['meterserialno']) ?></h1>


<div>
<?php echo Html::beginForm(['add-meter'],'post'); 
      echo Html::hiddenInput('MeterId', $model->MeterId);
?>

    <div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Type')." :"); ?> </div>
    	<div class="col-md-2 ">
        	<?php	echo Html::input('text','metermodel',$passport['metermodel'],['id'=>'metermodel','class'=>'form-control','placeholder'=>Yii::t('meter','Type'),'list'=>'dlTypesList']); ?> 
        	<?php	echo '<datalist id="dlTypesList">';
					echo Html::renderSelectOptions(null,$mtypes);    
					echo '</datalist>';
			?>
        </div> 
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Serial №')." :"); ?> </div>
    	<div class="col-md-2"> 
			<?php echo Html::input('text','meterserialno',$passport['meterserialno'],['id'=>'meterserialno','class'=>'form-control','placeholder'=>Yii::t('meter','Serial №')]); ?> 
    	</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Phases')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::dropDownList('meterphases', $passport['meterphases'], [1=>'1',2=>'2',3=>'3'],['id'=>'meterphases','class'=>'form-control']); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Digits')." :"); ?> </div>
		<div class="col-md-2">
			<?php 
				$tmdig = [5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10'];
				if (!$IsNewMeter) {
					if ($passport['meterdigits']<5) $tmdig = [$passport['meterdigits']=>"{$passport['meterdigits']}"]+$tmdig;
					if ($passport['meterdigits']>10) $tmdig += [$passport['meterdigits']=>"{$passport['meterdigits']}"];
				}
				echo Html::dropDownList('meterdigits', $passport['meterdigits'], $tmdig,['id'=>'meterdigits','class'=>'form-control']); 
			?> 
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Current')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metercurrent',$passport['metercurrent'],['id'=>'metercurrent','class'=>'form-control','placeholder'=>Yii::t('meter','Current')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Current max')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metermaxcurrent',$passport['metermaxcurrent'],['id'=>'metermaxcurrent','class'=>'form-control','placeholder'=>Yii::t('meter','Current max')]); ?> 
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Voltage')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metervoltage',$passport['metervoltage'],['id'=>'metervoltage','class'=>'form-control','placeholder'=>Yii::t('meter','Voltage')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','System №')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metersysno',$passport['metersysno'],['id'=>'metersysno','class'=>'form-control','placeholder'=>Yii::t('meter','System №')]); ?> 
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Comm №')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metercomno',$passport['metercomno'],['id'=>'metercomno','class'=>'form-control','placeholder'=>Yii::t('meter','Comm №')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','IMEI')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meterimei',$passport['meterimei'],['id'=>'meterimei','class'=>'form-control','placeholder'=>Yii::t('meter','IMEI')]); ?> 
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Phone')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meterphone',$passport['meterphone'],['id'=>'meterphone','class'=>'form-control','placeholder'=>Yii::t('meter','Phone')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','IP')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meterip',$passport['meterip'],['id'=>'meterip','class'=>'form-control','placeholder'=>Yii::t('meter','IP')]); ?> 
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Calibr. period')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','metecalibrationinterval',$passport['metecalibrationinterval'],['id'=>'metecalibrationinterval','class'=>'form-control','placeholder'=>Yii::t('meter','Calibr. period')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Calibr. data')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo DatePicker::widget(['name'  => 'metecalibrationdata',		
                                    'value'  => $passport['metecalibrationdata'],
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]);
            ?>
		</div>
	</div>

	<div class="row">
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Owner')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meterowner',$passport['meterowner'],['id'=>'meterowner','class'=>'form-control','placeholder'=>Yii::t('meter','Calibr. period')]); ?> 
		</div>
    	<div class="col-md-1"></div>
    	<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Inventory №')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meterinventoryno',$passport['meterinventoryno'],['id'=>'meterinventoryno','class'=>'form-control','placeholder'=>Yii::t('meter','Inventory №')]); ?> 
		</div>
	</div>

	<div class="row">
		<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Account')." :"); ?> </div>
		<div class="col-md-2">
			<?php echo Html::input('text','meteraccno',$passport['meteraccno'],['id'=>'meteraccno','class'=>'form-control','placeholder'=>Yii::t('meter','Account')]); ?> 
		</div>
		<div class="col-md-1"></div>
		<div class="col-md-2"> <?php echo Html::label(Yii::t('meter','Account name')." :"); ?> </div>
		<div class="col-md-5">
			<?php echo Html::input('text','meteraccname',$passport['meteraccname'],['id'=>'meteraccname','class'=>'form-control','placeholder'=>Yii::t('meter','Account name')]); ?> 
		</div>
	</div>

	<div class="row">
		<div class="col-md-1">
			<?php echo Html::label(Yii::t('ticketinputform','Region')).":"; ?>
		</div>
		<div class="col-md-3">
			<?php echo Html::dropDownList('districtcode', $passport['districtcode'], ArrayHelper::map($regions,'districtcode','districtname'), ['id'=>'districtcode','class'=>'form-control','onChange'=>'onSelectRegion()']); ?> 
		</div>
		<div class="col-md-1"></div>
		<div class="col-md-2">
			<?php echo Html::label(Yii::t('ticketinputform','Street')).":"; ?>
		</div>
		<div class="col-md-3">
			<?php 
				echo   Select2Widget::widget([
					'id' => 'fastreet_id',
					'name' => 'fastreet_id',
					'settings' => [ 'width' => '100%', 'val' => "611" ],                 
					'events' => [ 'select2:select' =>'onSelectStreet' ],
					'items' => $streets,
					'value' => $passport['fastreet_id'],
				]);            
			?>
		</div>
	</div>

	<div class="row">

		<div class="col-md-2">
			<?php echo Html::label(Yii::t('ticketinputform','Building')).":"; ?>
		</div>
		<div class="col-md-2">
			<?php 
				echo   Select2Widget::widget([
					'name' => 'meterfacility_id',
					'id' => 'meterfacility_id',
					'settings' => [ 'width' => '100%' ],                 
					'events' => [ 'select2:select' =>'onSelectFacility'],
					'items' => $fasilities,
					'value' => $passport['meterfacility_id'],
				]);
			?>
		</div>

		<div class="col-md-1"></div>

		<div id="divEntranceInput" style='display:none2;'>
			<div class="col-md-2" >
				<?php echo Html::label(Yii::t('ticketinputform','Entrance')); ?>
			</div>
			<div class="col-md-2" >
				<?php echo Html::input('text','meterporchno',$passport['meterporchno'],['id'=>'meterporchno','class'=>'form-control']); ?> 
			</div>
		</div>

	</div>

	<div class="row"> <br>
	</div>


<div class="row">
  <div class="col-md-offset-1">
    <?php echo Html::submitButton(Yii::t('meter','Save'),['id' => 'SubmitButton', 'class'=>'submit btn btn-success']); ?>
  </div>
</div>

<?php
echo Html::endForm();
?>

</div>

<SCRIPT>
function onSelectRegion()
{
    $.ajax({
         url: "<?php echo Url::toRoute(["/ticket-input/get-streets-list"]); ?>",
         type: "POST",
         dataType: "json",
         data: {District: $("#districtcode").val()},
         success: function(datamas) {
                $("#fastreet_id").html("");
                $("#fastreet_id").select2({data:datamas, width:'100%'});
                onSelectStreet();
         },
         error:   function() {
                $("#fastreet_id").html('AJAX error!');
         }

  });
  return false;
}

function onSelectStreet()
{
    $.ajax({
         url: "<?php echo Url::toRoute(["/ticket-input/get-facility-list"]); ?>",
         type: "POST",
         dataType: "json",
         data: {StreetId: $("#fastreet_id").val()},
         success: function(datamas) {
                $("#meterfacility_id").html("");
                $("#meterfacility_id").select2({data:datamas, width:'100%'});
                onSelectFacility();
         },
         error:   function() {
                $("#meterfacility_id").html('AJAX error!');
         }

  });
  return false;
}

function onSelectFacility()
{
    $.ajax({
         url: "<?php echo Url::toRoute(["/ticket-input/get-porches-number"]); ?>",
         type: "POST",
         dataType: "json",
         data: {facility_id: $("#meterfacility_id").val()},
         success: function(data) {
         	console.log('подъездов: '+data);
         	if (data>1)
                $("#divEntranceInput").show();
            else {
                $("#divEntranceInput").hide();
                $("#meterporchno").val("");
            }
         },
         error:   function() {
                $("#divEntranceInput").html('AJAX error!');
         }

  });
  return false;
}

</SCRIPT>

