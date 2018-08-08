<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Occupation */

$this->title =  $model->upname; //'Изменение Изменение Должности';
$this->params['breadcrumbs'][] = ['label' => 'Справочник Должностей', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['','id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Изменение';
/*
$this->title = $model->upfullname; //'Изменение данных' ;
$this->params['breadcrumbs'][] = ['label' => 'Справочник Персонала', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];
*/
?>
<div class="occupation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
