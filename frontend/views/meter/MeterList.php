<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Tickets;
use yii\grid\GridView;


$this->title = Yii::t('meter','Meter list');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-holder">
	<h1><?= Html::encode($this->title) ?></h1>
   	<?php  

        $mtrColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => "Номер",
                'content' => function($data){
                	return "<a href=".Url::toRoute(['meter/meter-info']).'&MeterId='.$data['id'].' target="_blank">'.$data['meterserialno'].'</a>';
                }
            ],
            [
                'label' =>"Тип",
                'attribute' => 'metermodel',
            ],
            [
                'label' =>"Адрес",
                'attribute' => 'addrstr',
            ],

            [
                'label' =>"Дата",
                'attribute' => 'mdatatime',
            ],
            [
                'label' =>"Показания",
                'attribute' => 'mdata',
            ],
        ];

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $mtrColumns, 
		]);
	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>	

