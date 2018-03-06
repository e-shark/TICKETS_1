<?php

use yii\helpers\Html;
//use yii\helpers\Url;
use yii\bootstrap\Tabs;

//---assemble string with 1562 number 
$ti1562nostr = $model->ticket['ticoderemote'] ?
        " <span style='font-size:60%;color:#E9967A'>(<span style='font-size:60%;color:#E9967A'> №1562</span> ".$model->ticket['ticoderemote'].')</span>':'';

$this->title = /*Yii::t('app','Ticket')*/'Заявка'.' № '.$model->ticket['ticode'];

//---Find the referrer & set the path to view
$refcontroller=Yii::$app->request->getReferrer(); //echo $refcontroller;
 if( FALSE !== strstr($refcontroller,'reports')){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['reports/index']];
    if( FALSE !== strstr($refcontroller,'ticketslist'))
        $this->params['breadcrumbs'][] = ['label' => 'Отчет: Список Заявок', 'url' => $refcontroller];
    if( FALSE !== strstr($refcontroller,'oosnow'))
        $this->params['breadcrumbs'][] = ['label' => 'Отчет по неработающим лифтам', 'url' => $refcontroller];
 }
else if( FALSE!==strstr($refcontroller,'tickets')){
    $this->params['breadcrumbs'][] = ['label' => /*Yii::t('app','Tickets')*/'Заявки', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tickets-view">

    <h1><?= Html::encode($this->title).$ti1562nostr." ".Html::label(($model->oosHours>24)? 'Часов простоя : '.$model->oosHours:"",null,['class'=>'label label-danger'])?></h1>
    <?= Tabs::widget([
        'items' => [
            [
            'label' => $this->title,
            'content' => $this->render('_viewtab', ['model' => $model]),
            'active' => true
            ],
            [
            'label' => Yii::t('app','Ticket history'),
            'content' => $this->context->renderpartial('_historytab', ['model' => $model]),
            ],
            [
            'label' => Yii::t('app','Ticket spair parts'),
            'content' => $this->context->renderpartial('_sparttab', ['model' => $model]),
            ],
            [
            'label' => Yii::t('app','Photo'),
            //'content' => $this->context->renderpartial('_uploadtab', ['model' => $model,'imagemodel' => $imagemodel]),
            'content' => $this->context->renderpartial('_docstab', ['model' => $model,'imagemodel' => $imagemodel]),
            ],
        ],
    ])?>
</div>
