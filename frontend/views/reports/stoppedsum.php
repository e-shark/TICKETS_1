<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Количество остановок лифтов по районам';
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

    <?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>

   	<?php  
        $tiColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>"Район",
                'attribute' => 'RIG',
            ],
            [
                'label' =>"<5*",
                'attribute' => 'SUM4',
                'content'=> function($data){return (empty($data['SUM4'])?"-":$data['SUM4']);},

            ],
            [
                'label' =>"5",
                'attribute' => 'SUM5',
                'content'=> function($data){return (empty($data['SUM5'])?"-":$data['SUM5']);},

            ],
            [
                'label' =>"6",
                'attribute' => 'SUM6',
                'content'=> function($data){return (empty($data['SUM6'])?"-":$data['SUM6']);},

            ],

            [
                'label' =>"7",
                'attribute' => 'SUM7',
                'content'=> function($data){return (empty($data['SUM7'])?"-":$data['SUM7']);},

            ],
            [
                'label' =>"8",
                'attribute' => 'SUM8',
                'content'=> function($data){return (empty($data['SUM8'])?"-":$data['SUM8']);},

            ],
            [
                'label' =>"9",
                'attribute' => 'SUM9',
                'content'=> function($data){return (empty($data['SUM9'])?"-":$data['SUM9']);},

            ],
            [
                'label' =>"10",
                'attribute' => 'SUM10',
                'content'=> function($data){return (empty($data['SUM10'])?"-":$data['SUM10']);},

            ],
            [
                'label' =>"11",
                'attribute' => 'SUM10',
                'content'=> function($data){return (empty($data['SUM11'])?"-":$data['SUM11']);},

            ],
            [
                'label' =>"12",
                'attribute' => 'SUM12',
                'content'=> function($data){return (empty($data['SUM12'])?"-":$data['SUM12']);},

            ],
            [
                'label' =>"13",
                'attribute' => 'SUM13',
                'content'=> function($data){return (empty($data['SUM13'])?"-":$data['SUM13']);},

            ],
            [
                'label' =>"14",
                'attribute' => 'SUM14',
                'content'=> function($data){return (empty($data['SUM14'])?"-":$data['SUM14']);},

            ],
            [
                'label' =>"15",
                'attribute' => 'SUM15',
                'content'=> function($data){return (empty($data['SUM15'])?"-":$data['SUM15']);},

            ],
            [
                'label' =>"16",
                'attribute' => 'SUM16',
                'content'=> function($data){return (empty($data['SUM16'])?"-":$data['SUM16']);},

            ],


            [
                'label' =>">16",
                'attribute' => 'MOR16',
                'content'=> function($data){return (empty($data['MOR16'])?"-":$data['MOR16']);},

            ],

            [
                'label' =>"Всего:",
                'attribute' => 'SUMALL',
                'content'=> function($data){return (empty($data['SUMALL'])?"-":$data['SUMALL']);},

            ],
        ];

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $tiColumns, 
		]);
        echo "* Количество остановок, шт.<br>";

	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>