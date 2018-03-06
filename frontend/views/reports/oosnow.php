<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Отчет по неработающим лифтам';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
        h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        button#submitFltr1, input#printButton,  div#divtivexecutant,div#divtifindstr, div#reportpagesize { display: none; } 
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
    		'columns' => [
            	['class' => 'yii\grid\SerialColumn'],
            	[
                'label' =>"Время инцидента",
                'format'=>['date','dd-MM-yyyy  HH:m'],
                'attribute' => 'tiincidenttime',
            	],
            	[
                'label' =>"Часов простоя",
                'attribute' => 'ooshours',
            	],
            	[
                'label' =>"Номер лифта",
                'attribute' => 'tiobjectcode',
            	],
            	[
                'label' =>"Адрес",
                'attribute' => 'tiaddress',
            	],
            	[
                'label' =>"Номер заявки",
                'attribute' => 'ticode',
                'content' => function($data){ 
                    $url = Url::toRoute(['tickets/view', 'id' => $data['id']]);
                    return "<a href=$url>".$data['ticode'].'</a>';}
            	],
            	[
                'label' =>"Сервисное подразделение",
                'attribute' => 'divisionname',
            	],
            ]
		]);
	?>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>
