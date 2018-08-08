<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Elevator */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Elevators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="elevator-view">
 <?= $this->render('_form1', [
        'model' => $model,
    ]) ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'elremoteid',
            'eldevicetype',
            'elserialno',
            'elmodel',
            'eldate',
            'elload',
            'elspeed',
            'elstops',
            'eldoortype',
            'eltype',
            'elporchno',
            'elporchpos',
            'elinventoryno',
            'elregyear',
            'elrtu_id',
            'elfacility_id',
            'eldivision_id',
            'elperson_id',
        ],
    ]) ?>

</div>
