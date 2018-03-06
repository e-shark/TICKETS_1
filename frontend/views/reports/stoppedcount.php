<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Отчет по количеству остановленных лифтов';
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
                'attribute' => 'districtname',
            ],
            [
                'label' =>"Лифтов<br>всего",
                'encodeLabel' => false,
                'attribute' => 'allcount',
            ],

        ];
        $intvl = $model->GetIntervals();
        unset($key);
        foreach ($intvl as $key => $value) {
            $tiColumns[]=[
                //'label' =>"c:&nbsp;".date( "d-m-Y", $value['from'] )."<br> по:&nbsp;".date( "d-m-Y", $value['to'] ),
                'label' =>"<div class=\"text-center\">".date( "d-m-Y", $value['from'] )."<br>-<br>".date( "d-m-Y", $value['to'] )."</div>",
                'encodeLabel' => false,
                'format' => 'html',
                'content' => function($data) use ($key){ 
                    $ss= "  sum{$key}  ";
                    return "<div class=\"text-right\">".(empty($data["sum{$key}"])?"-":$data["sum{$key}"])."<br>".round(100*$data["sum{$key}"]/$data['allcount'],2)."%"."</div>";
                },
            ];
        }

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $tiColumns, 
		]);

	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>