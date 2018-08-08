<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Division */

$this->title = $model->updivisionname; //'Изменение данных' ;
$this->params['breadcrumbs'][] = ['label' => 'Справочник Подразделений', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->divisionname, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'divisionname' => $model->id]];

?>
<div class="division-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
