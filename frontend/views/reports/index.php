<?php
use yii\helpers\Html;
use frontend\models\Tickets;


$this->title = Yii::t('app','Reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reports-index">
	<?= '<p>'.Html::a('Список Заявок', ['ticketslist'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по выполнению заявок', ['titotals'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по неработающим лифтам', ['oosnow'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по повторным заявкам', ['repfailures'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по поступлению заявок по дням', ['tiperday'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по поступлению заявок по месяцам', ['tipermonth'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Работа Аварийной Службы', ['tilas'], ['class' => 'btn btn-success']).'</p>' ?>
	<?= '<p>'.Html::a('Отчет по выполнению заявок 1562', ['titotals1562'],['class' => 'btn btn-success']).'</p>' ?>

     <?php 	if(FALSE!==Tickets::getUserOpRights()){// Reports below intended for use by organization staff only?>
	<?= '<br><p>'.Html::a('Журнал экспорта в систему ИТЕРА', ['iteralog'],['class' => 'btn btn-success']).'</p>' ?>
	<?php }?>


</div>