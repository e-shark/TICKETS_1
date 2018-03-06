<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use frontend\models\Tickets;
use yii\widgets\ActiveForm;
use frontend\models\Report_Titotals;


$this->title = 'Отчет по выполнению заявок 1562';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
        h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        button#submitFltr1,input#printButton,   div#ditivexecutant,div#divtifindstr { display: none; } 
        .report-holder a[href]::after {content: "";}
}');
$this->registerJs( 'function print_page(){window.print() ;}', yii\web\View::POS_HEAD);

$rsttss=Report_Titotals::getStatusesListRemote();
?>

<div class="report-holder">
	<h1><?= Html::encode($this->title) ?></h1>


  <?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>


	 <?=  GridView::widget([
			'dataProvider' => $provider,
            'layout'=>"{sorter}\n{pager}\n{items}",
            'rowOptions'=> 
                function ($model, $key, $index, $grid){
                    if($model['tistatusremote']=='Итого')
                            return ['style' => 'background-color:#778899;font-weight: bold;'];
                    else if(strstr($model['tistatusremote'],'Закр. КАО')){
                        if(!in_array($model['tistatus'],['KAO_COMPLETE']))
                            return ['style' => "background-color:#fcff33;font-weight: bold;"];
                    }
                    else if(strstr($model['tistatusremote'],'Вып. исп')){
                        if(!in_array($model['tistatus'],['DISPATCHER_COMPLETE','OPERATOR_COMPLETE']))
                            return ['style' => "background-color:#fcff33;font-weight: bold;"];
                    }
                    else if(strstr($model['tistatusremote'],'Принята') || strstr($model['tistatusremote'],'Нужно')){
                        if(in_array($model['tistatus'],['DISPATCHER_COMPLETE','OPERATOR_COMPLETE']))
                            return ['style' => "background-color:#fcff33;font-weight: bold;"];
                    }
                },
			'columns' => [
            	[
                'label' =>"Статус 1562",
                'attribute' => 'tistatusremote',
                'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            	],
            	[
                'label' =>"Статус КСП ХГЛ",
                'attribute' => 'tistatus',
                'format'=>'html',
                'value' => function($data){ return empty($data['tistatus'])?'':Yii::$app->params['TicketStatus'][$data['tistatus']];},
                'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            	],
                [
                'label' =>"Итого",
                'attribute' => 'total',
                ],
            	[
                'label' =>"Инд",
                'attribute' => 'Ind',
            	],
            	[
                'label' =>"Киев",
                'attribute' => 'Kyi',
                'content' => function($data) use ($model,$rsttss) { 
                    $url = Url::toRoute(['reports/ticketslist', 'district' => "КИЇВСЬКИЙ",'calltype' => '1562','datefrom' => $model->datefrom,'dateto' => $model->dateto,'status'=>$data['tistatus'],'statusremote'=>array_search($data['tistatusremote'],$rsttss)]);
                    return "<a href=$url>".$data['Kyi'].'</a>';}
            	],
            	[
                'label' =>"Мос",
                'attribute' => 'Mos',
            	],
            	[
                'label' =>"Нем",
                'attribute' => 'Nem',
            	],
                [
                'label' =>"Нов",
                'attribute' => 'Nov',
                ],
                [
                'label' =>"Осн",
                'attribute' => 'Osn',
                ],
                [
                'label' =>"Слоб",
                'attribute' => 'Slo',
                ],
                [
                'label' =>"Шев",
                'attribute' => 'She',
                ],
            ]
		]);
	?>

    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>