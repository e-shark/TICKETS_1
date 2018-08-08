<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = 'ОЗК ОДС КСП "Харьковгорлифт"';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Приветствуем Вас!</h1>

    <p>  
        <?= Html::a('Список должностей',  ['/employeeeq/occupation/index'], ['class'=>'btn btn-lg btn-success']) ?>
    </p>
        <p><a class="btn btn-lg btn-success" href="/employeeeq/occupation/index">Список Персонала</a></p>
        <p><a class="btn btn-lg btn-success" href="/occupation">Список Должностей</a></p>
        <p><a class="btn btn-lg btn-success" href="/division">Список Подразделений</a></p>
        <p><a class="btn btn-lg btn-success" href="/elevator">Список Оборудования</a></p>

     </div>
</div>