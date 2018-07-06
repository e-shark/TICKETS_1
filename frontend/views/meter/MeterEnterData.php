<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('meter','Input of readings')." ".Yii::t('meter','for meter')." ".$passport['meterserialno'];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="panel panel-default">
  <div class="panel-heading"><?php echo Html::label(Yii::t('meter','Input of readings')); ?></div>
  <div class="panel-body">


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']],'post') ?>

    <!--div class="row panel panel-info"-->
        <div class="col-md-4">
    	<div class="input-group">
    		<span class="input-group-addon">Показания</span>
			<?php echo Html::input('text','MeterData','',['id'=>'MeterData','class'=>'form-control']); ?> 
    	</div>
    	</div>
        <div class="col-md-4">
			<?php // echo Html::hiddenInput('imageFile', ""); ?>
			<?php echo Html::input('file','imageFile','',['id'=>'imageFile','class'=>'form-control', 'accept'=>"image/*,image/jpeg"]); ?> 
			<?php //echo $form->field($imagemodel, 'imageFile')->fileInput() ?>
    	</div>
    	<?php Yii::warning("********************** refurl=[".$refurl)."]"; ?>
		<?php echo Html::hiddenInput('MeterId', $passport['id']); ?>
		<?php echo Html::hiddenInput('RefUrl', $refurl); ?>
        <div class="col-md-1">
			<?= '<br>'.Html::submitButton(Yii::t('app','Add'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['add-reading'])]) ?>
       	</div>

<?php ActiveForm::end() ?>

  </div>
</div>