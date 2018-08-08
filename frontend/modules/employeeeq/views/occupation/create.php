<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Occupation */

$this->title = 'Добавление Должности';
//$this->params['breadcrumbs'][] = ['label' => 'Occupations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Справочник Должностей', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="occupation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
