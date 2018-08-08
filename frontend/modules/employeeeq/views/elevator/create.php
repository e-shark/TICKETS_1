<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Elevator */

$this->title = 'Create Elevator';
$this->params['breadcrumbs'][] = ['label' => 'Elevators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="elevator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
