<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/*
 */
//use Yii;
use yii\grid\GridView;

//echo $model->sqls.'\n';
//print_r($model->params);

$this->title = 'Отчет: Список Заявок';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
		h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        th:last-child,td:last-child,	
        input[type="image"],button[type="submit"],  	div#divtivexecutant,div#divtifindstr, div#reportpagesize { display: none; } 
        .report-holder a[href]::after {content: "";}
        div.report-holder *:not(h1){font-size:12px}
}');
$this->registerJs( 'function print_page(){window.print() ;}', yii\web\View::POS_HEAD);
?>

<div class="report-holder">
	<h1><?= Html::encode($this->title) ?></h1>

	<?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>

	<div id='ticketsIndexGrid'>
	    	<?php echo $this->render('/tickets/_index.php', [ 'provider'=>$provider ,'model'=>$model]); ?>
	</div>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>
    
<?php/*<code><?= __FILE__ ?></code>*/?>
