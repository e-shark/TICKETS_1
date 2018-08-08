<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Employee */

$this->title = 'Добавление Сотрудника';//'Create Employee';
$this->params['breadcrumbs'][] = ['label' => 'Справочник Персонала', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title; было
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
<div class="employee-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
