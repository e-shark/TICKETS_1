<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Перечень лифтов';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
        h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        input[type="image"],button[type="submit"],      div#divtivexecutant,div#divtifindstr, div#reportpagesize { display: none; } 
        .report-holder a[href]::after {content: "";}
        div.report-holder *:not(h1){font-size:12px}
}');
$this->registerJs( 'function print_page(){window.print() ;}', yii\web\View::POS_HEAD);
?>

<div class="report-holder">
	<h1><?= Html::encode($this->title) ?></h1>
   	<?php  

        $tiColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>"Район",
                'attribute' => 'districtname',
            ],
            [
                'label' =>"№",
                'attribute' => 'elremoteid',
            ],
            [
                'label' =>"Улица",
                //'attribute' => 'streettype',
                'content' => function($data){
                	return $data['streettype']." ".$data['streetname'];
                }
            ],
            [
                'label' =>"Дом",
                'attribute' => 'faaddressno',
            ],
            [
                'label' =>"Подъезд",
                'content' => function($data){
                	return $data['elporchno']." ".$data['elporchpos'];
                }
            ],
            [
                'label' =>"Тип",
                'attribute' => 'eltype',
            ],

            [
                'label' =>"г.п.",
                'attribute' => 'elload',
            ],
            [
                'label' =>"скор.",
                'attribute' => 'elspeed',
            ],
            [
                'label' =>"ост.",
                'attribute' => 'elstops',
            ],
            [
                'label' =>"год",
                'attribute' => 'elregyear',
            ],
            [
                'label' =>"обсл.",
                'attribute' => 'divisionname',
            ],

        ];

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $tiColumns, 
		]);
	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>	
