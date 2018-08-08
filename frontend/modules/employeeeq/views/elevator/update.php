<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Elevator */

$this->title = 'Update Elevator: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Elevators', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="elevator-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
