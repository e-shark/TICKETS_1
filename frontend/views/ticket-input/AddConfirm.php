<?php

use yii\helpers\Url;
use yii\helpers\Html;
use zxbodya\yii2\galleryManager\GalleryManager;
use himiklab\colorbox\Colorbox;
use frontend\models\Product;

$this->title = Yii::t('ticketinputform','Add confirm');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title)?></h1> 

<?php if (0 != $model->recid) {?>

<?php echo Html::label(Yii::t('ticketinputform','Ticket number')).' : '; ?>
<a class="btn btn-lg btn-success" href=<?php  echo '"'.Url::toRoute(['tickets/view', 'id' => $model->recid]).'"'; ?> ><?=$model->ticode;?></a>

<br>

<?php echo Html::label(Yii::t('ticketinputform','Add time')).' : '.$model->tiopenedtime; ?>

<div class="tickets-_uploadtab">

	<?php
		$IMGModel = new Product();
	 	$IMGModel->vOwnerId = 0 + $model->recid;
	    //if ($IMGModel->isNewRecord) {
	    //    echo 'Can not upload images for new record';
	    //} else {
		    echo GalleryManager::widget(
		        [
		            'behaviorName' => 'galleryBehavior',
		            'model' => $IMGModel,
		            'apiRoute' => 'tickets/galleryApi',
		            'GrantEdit' => $model->isUserMaster(),
		        ]
		    );
		//}

	?>

	<?= Colorbox::widget([
	    'targets' => [
	        '.image-orig-href' => [
	            'maxWidth' => 1000,
	            'maxHeight' => 700,
	            'opacity' => 0.6,
	        ],
	    ],
	    'coreStyle' => 1
	]) ?>


</div>

<?php } else { echo Html::label(Yii::t('ticketinputform','Add ticket error')); } ?>

<br>
<a class="btn btn-lg btn-success" href=<?php  echo '"'.Url::toRoute('ticket-input/inputform').'"'; ?> ><?=Yii::t('ticketinputform','Next ticket')?></a>
<a class="btn btn-lg btn-success" href=<?php  echo '"'.Url::toRoute('tickets/index').'"'; ?> ><?=Yii::t('ticketinputform','Ticket list')?></a>

