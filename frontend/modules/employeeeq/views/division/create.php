<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Division */

$this->title = 'Добавление Подразделения';
$this->params['breadcrumbs'][] = ['label' => 'Справочник Подразделений', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="division-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ])  ?>

</div>
