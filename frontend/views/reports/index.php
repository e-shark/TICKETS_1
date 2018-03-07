<?php
use yii\helpers\Html;
use frontend\models\Tickets;


$this->title = Yii::t('app','Reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reports-index">
    <P>Список отчетов:</P>
	<ul>
    <li><?= Html::a('Список Заявок', ['ticketslist'], []) ?></li>
    <li><?= Html::a('Отчет по выполнению заявок', ['titotals'], []) ?></li>
    <li><?= Html::a('Отчет по неработающим лифтам', ['oosnow'], []) ?></li>
    <li><?= Html::a('Отчет по повторным заявкам', ['repfailures'], []) ?></li>
    <li><?= Html::a('Отчет по поступлению заявок по дням', ['tiperday'], []) ?></li>
    <li><?= Html::a('Отчет по поступлению заявок по месяцам', ['tipermonth'], []) ?></li>
    <li><?= Html::a('Работа Аварийной Службы', ['tilas'], []) ?></li>
    <li><?= Html::a('Отчет по выполнению заявок 1562', ['titotals1562'],[]) ?></li>
    <li><?= Html::a('Список остановленных и запущенных лифтов', ['stopped-list'],[]) ?></li>
    <li><?= Html::a('Количество остановленных лифтов по районам', ['stopped-sum'],[]) ?></li>
    <li><?= Html::a('Отчет по количеству остановленных лифтов', ['stopped-count'],[]) ?></li>
    <br>
    <?php if(FALSE!==Tickets::getUserOpRights()){// Reports below intended for use by organization staff only?>
    <li><?= Html::a('Журнал экспорта в систему ИТЕРА', ['iteralog'],[]) ?></li>
    <?php } ?>

</div>