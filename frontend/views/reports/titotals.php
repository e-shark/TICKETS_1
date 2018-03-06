<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use frontend\models\Tickets;
use yii\widgets\ActiveForm;


$this->title = 'Отчет по выполнению заявок';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
        h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        button#submitFltr1,input#printButton,   div#ditivexecutant,div#divtifindstr { display: none; } 
        .report-holder a[href]::after {content: "";}
        div.report-holder *:not(h1){font-size:12px}
}');
$this->registerJs( 'function print_page(){window.print() ;}', yii\web\View::POS_HEAD);
?>

<div class="report-holder">
	<h1><?= Html::encode($this->title) ?></h1>


  <?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>


	 <?=  GridView::widget([
			'dataProvider' => $provider,
            'layout'=>"{sorter}\n{pager}\n{items}",
            'rowOptions'=> function ($model, $key, $index, $grid){
                if($model['tiobjectcode']=='TOTAL')return ['style' => 'background-color:#778899;font-weight: bold;'];
            },
			'columns' => [
            	[
                'label' =>"Объект",
                'attribute' => 'tiobject',
                'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            	],
            	[
                'label' =>"Всего заявок",
                'attribute' => 'total',
                'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            	],
            	[
                'label' =>"Закрыто",
                'attribute' => 'completed',
            	],
            	[
                'label' =>"Отозвано",
                'attribute' => 'revoked',
            	],
            	[
                'label' =>"Новые",
                'attribute' => 'assigned',
            	],
            	[
                'label' =>"В работе",
                'attribute' => 'atwork',
            	],
            ]
		]);
	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>