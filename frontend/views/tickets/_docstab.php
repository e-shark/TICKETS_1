<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\helpers\ArrayHelper;
use zxbodya\yii2\galleryManager\GalleryManager;

use frontend\models\Product;
use himiklab\colorbox\Colorbox;

/**
 *	Ticket spare part partial view
 */
//$IMGModel = new Product();
//$IMGModel = Product::findOne($model->ticket['id']);
//$IMGModel = Product::findOne(10);
//$IMGModel = Product::find()->where(['ownerId' => $model->ticket['id']])->one();
$IMGModel = new Product();
 $IMGModel->vOwnerId = 0 + $model->ticket['id'];
//$IMGModel->vOwnerId = 33;
/*
//$IMGModel::find()->where(['ownerId' => $model->ticket['id']])->one();
$IMGModel::find()->where(['ownerId' => 11])->one();
if (is_null($IMGModel)) {
//	$IMGModel->setAttributes(['ownerId' => $model->ticket['id']]);
	$IMGModel->setAttributes(['ownerId' => 11]);
	echo var_dump( $IMGModel->getAttributes() );
}
	$IMGModel->setAttributes(['ownerId' => 11]);
	echo var_dump( $IMGModel->getAttributes()); 
//$IMGModel->findOne($model->ticket['id']);
*/
?>

<div class="tickets-_uploadtab">

<?php
    //if ($IMGModel->isNewRecord) {
    //    echo 'Can not upload images for new record';
    //} else {
	    echo GalleryManager::widget(
	        [
	            'behaviorName' => 'galleryBehavior',
	            'model' => $IMGModel,
	            'apiRoute' => 'tickets/galleryApi',
	            'GrantEdit' => ($model->isUserMaster() || $model->isUserDispatcher()),
	        ]
	    );
	//}

/*  Так можно рисовать картинки
foreach($IMGModel->getBehavior('galleryBehavior')->getImages() as $image) {
	echo '<div class = "group1">'; 
    echo Html::a( Html::img($image->getUrl('preview')) , $image->getUrl('medium'),['class'=>"group2"]);
   	echo '</div>';

}
*/
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

<?php

/*
$script = <<< JS
    //jQuery('.image-orig-href').colorbox({ opacity:0.3, rel:'group', maxWidth:800, maxHeight:600});
    //jQuery(".group1").lightBox();
JS;
$this->registerJs($script, yii\web\View::POS_END);
*/
?>
</div>
