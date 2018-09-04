<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Отчет по простоям лифтов при ремонте';
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

<div class="report-stopped-list report-holder">
	<h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>

   	<?php  
        $tiColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' =>"Район",
                'attribute' => 'tiregion',
            ],
            [
                'label' =>"Номер лифта",
                //'attribute' => 'tiobjectcode',
                'format' => 'html',
                'content'=> function($data){
                    return  $data['tiobjectcode'];
					//$url = Url::toRoute(['reports/ticketslist', 'tifindstr' => $data['tiobjectcode']]);
					//return  "<a href=$url>".$data['tiobjectcode'].'</a>';
				},
			],
            [
                'label' =>"Адрес",
                'attribute' => 'tiaddress',
            ],
            [
                'label' =>"Часов простоя",
                //'attribute' => 'oostime',
                'format' => 'html',
                'content'=> function($data){
                	$h = $data['oosumtime'];
                	return ($h>24?"<b style='font-size: large; color:red;'>{$h}</b>":"<b>{$h}</b>");
                }
            ],
            [
                'label' =>"Даты",
                //'attribute' => 'tioosbegin',
                'format' => 'html',
                'content'=> function($data){
                	return "<b>".(new DateTime($data['tioosbegin'], new DateTimeZone("UTC")))->format('d-m-Y H:i:s')."</b> -&nbsp;остановка <br>".
                    "";},
                    /*
                	(empty($data['tiplannedtimenew'])?"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;":(new DateTime($data['tiplannedtimenew'], new DateTimeZone("UTC")))->format('d-m-Y H:i:s'))." -&nbsp;план.пуска<br>".
                	(empty($data['tioosend'])?"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;":(new DateTime($data['tioosend'], new DateTimeZone("UTC")))->format('d-m-Y H:i:s')).
                	" -&nbsp;пуск";},
                    */
            ],
            [
                'label' =>"Заявок",
                //'attribute' => 'oostypetext',
                'format' => 'html',
                'content'=> function($data){ return count($data['tickets']); },
            ],
            [
                'label' =>"статус",
                'content' => function($data){ 
                    if (is_null($data['ep_status']))
                        $res = 'неизвестен';
                    else
                        switch ($data['ep_status']){
                            case 0: $res = "<span style='color:green;'>в работе</span>"; break;
                            case 1: $res = "<span style='color:red;'>остановлен</span>"; break;
                            default: $res = 'неизвестен'; break;
                        }
                    return $res;
                }
            ],
            [
                'label' =>"без транспорта",
                'content' => function($data){ 
                    return empty($data['ep_elnum'])?"V":"";
                }
            ],
        ];

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $tiColumns, 
		]);

	?>

    <h3>Сводный отчет по простоям</h3>
    <?php 
        $repColumns = [
            [
                'label' => "Район",
                'attribute' => 'District',
            ],
            [
                'label' => "Остановленых,<br>лифтов (часов)",
                'encodeLabel' => false,
                'attribute' => 'e1',
                'content' => function($data){ 
                    return $data['e1']."&nbsp;&nbsp;(".$data['h1'].")";
                }
            ],
            [
                'label' => "Статус неизвестен,<br>лифтов (часов)",
                'encodeLabel' => false,
                'content' => function($data){ 
                    return $data['e2']."&nbsp;&nbsp;(".$data['h2'].")";
                }
            ],
            [
                'label' => "В работе,<br>лифтов (часов)",
                'encodeLabel' => false,
                'content' => function($data){ 
                    return $data['e0']."&nbsp;&nbsp;(".$data['h0'].")";
                }
            ],
        ];
        echo GridView::widget([
            'dataProvider' => $report,
            'columns' => $repColumns, 
        ]);

    ?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>


</div>