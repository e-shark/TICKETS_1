<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Свод заявок 1562';
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
                'attribute' => 'tiregion',
            ],
            [
                'label' =>"Лифт",
                'attribute' => 'tiobjectcode',
            ],
            [
                //'label' =>"Заявок",
                'label' =>"<div style='height: 280px; width:20px;'> <div style='position:relative ; top: 260px; transform: rotate(-90deg)'>".str_replace(" ","&nbsp;","Заявок")."</div></div>",
                'encodeLabel' => false,
                'attribute' => 'XALL',
            ],
            [
                //'label' =>"Причина не определена",
                'label' =>"<div style='height: 280px; width:20px;'> <div style='position:relative ; top: 260px; transform: rotate(-90deg)'>".str_replace(" ","&nbsp;","Причина не определена")."</div></div>",
                'encodeLabel' => false,
                'content' => function($data){
                	$sum = $data['X0'] + $data['X39'] + $data['XX'] + $data['XM'];
                	return $sum>0?$sum:"";
                },
            ],

        ];

        /*
        if ( !empty($model->counters['sX99'])) $tiColumns[]=[ 'label' =>"Отмененных", 'content' => function($data){ return empty($data['X99'])?"":$data['X99']; }, ];
        if ( !empty($model->counters['sX1'])) $tiColumns[]=[ 'label' =>"Датчики", 'content' => function($data){ return empty($data['X1'])?"":$data['X1'] ; } ];
        if ( !empty($model->counters['sX2'])) $tiColumns[]=[ 'label' =>"Этажные переключатели", 'content' => function($data){ return empty($data['X2'])?"":$data['X2']; }, ];
        if ( !empty($model->counters['sX3'])) $tiColumns[]=[ 'label' =>"Концевые выключатели", 'content' => function($data){ return empty($data['X3'])?"":$data['X3']; }, ];
        if ( !empty($model->counters['sX4'])) $tiColumns[]=[ 'label' =>"Вызывные аппараты", 'content' => function($data){ return empty($data['X4'])?"":$data['X4']; }, ];
        if ( !empty($model->counters['sX5'])) $tiColumns[]=[ 'label' =>"Посты управления", 'content' => function($data){ return empty($data['X5'])?"":$data['X5']; }, ];
        if ( !empty($model->counters['sX6'])) $tiColumns[]=[ 'label' =>"Пост «Ревизия»", 'content' => function($data){ return empty($data['X6'])?"":$data['X6']; }, ];
        if ( !empty($model->counters['sX7'])) $tiColumns[]=[ 'label' =>"Световое табло", 'content' => function($data){ return empty($data['X7'])?"":$data['X7']; }, ];
        if ( !empty($model->counters['sX8'])) $tiColumns[]=[ 'label' =>"Защита электродвигателя", 'content' => function($data){ return empty($data['X8'])?"":$data['X8']; }, ];
        if ( !empty($model->counters['sX9'])) $tiColumns[]=[ 'label' =>"Неисправность эл. элементов схемы", 'content' => function($data){ return empty($data['X9'])?"":$data['X9']; }, ];
        if ( !empty($model->counters['sX10'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X10'])?"":$data['X10']; }, ];
        if ( !empty($model->counters['sX11'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X11'])?"":$data['X11']; }, ];
        if ( !empty($model->counters['sX12'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X12'])?"":$data['X12']; }, ];
        if ( !empty($model->counters['sX13'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X13'])?"":$data['X13']; }, ];
        if ( !empty($model->counters['sX14'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X14'])?"":$data['X14']; }, ];
        if ( !empty($model->counters['sX15'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X15'])?"":$data['X15']; }, ];
        if ( !empty($model->counters['sX16'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X16'])?"":$data['X16']; }, ];
        if ( !empty($model->counters['sX17'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X17'])?"":$data['X17']; }, ];
        if ( !empty($model->counters['sX18'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X18'])?"":$data['X18']; }, ];
        if ( !empty($model->counters['sX19'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X19'])?"":$data['X19']; }, ];
        if ( !empty($model->counters['sX20'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X20'])?"":$data['X20']; }, ];
        if ( !empty($model->counters['sX21'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X21'])?"":$data['X21']; }, ];
        if ( !empty($model->counters['sX22'])) $tiColumns[]=[ 'label' =>"", 'content' => function($data){ return empty($data['X22'])?"":$data['X22']; }, ];
        if ( !empty($model->counters['sX23'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X23',];
        if ( !empty($model->counters['sX24'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X24',];
        if ( !empty($model->counters['sX25'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X25',];
        if ( !empty($model->counters['sX26'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X26',];
        if ( !empty($model->counters['sX27'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X27',];
        if ( !empty($model->counters['sX28'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X28',];
        if ( !empty($model->counters['sX29'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X29',];
        if ( !empty($model->counters['sX30'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X30',];
        if ( !empty($model->counters['sX31'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X31',];
        if ( !empty($model->counters['sX32'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X32',];
        if ( !empty($model->counters['sX33'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X33',];
        if ( !empty($model->counters['sX34'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X34',];
        if ( !empty($model->counters['sX35'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X35',];
        if ( !empty($model->counters['sX36'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X36',];
        if ( !empty($model->counters['sX37'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X37',];
        if ( !empty($model->counters['sX38'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X38',];
        if ( !empty($model->counters['sX40'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X40',];
        if ( !empty($model->counters['sX60'])) $tiColumns[]=[ 'label' =>"", 'attribute' => 'X60',];
        */

        $model->FillColumnSet( $tiColumns, "X99", "Отмененных");
        $model->FillColumnSet( $tiColumns, "X1", "Датчики");
        $model->FillColumnSet( $tiColumns, "X2", "Этажные переключатели");
        $model->FillColumnSet( $tiColumns, "X3", "Концевые выключатели");
        $model->FillColumnSet( $tiColumns, "X4", "Вызывные аппараты");
        $model->FillColumnSet( $tiColumns, "X5", "Посты управления");
        $model->FillColumnSet( $tiColumns, "X6", "Пост «Ревизия»");
        $model->FillColumnSet( $tiColumns, "X7", "Световое табло");
        $model->FillColumnSet( $tiColumns, "X8", "Защита электродвигателя");
        $model->FillColumnSet( $tiColumns, "X9", "Неисправность эл. элементов схемы");
        $model->FillColumnSet( $tiColumns, "X10", "Реле НКУ");
        $model->FillColumnSet( $tiColumns, "X11", "Реле времени");
        $model->FillColumnSet( $tiColumns, "X12", "Автоматические выключатели");
        $model->FillColumnSet( $tiColumns, "X13", "Контактора");
        $model->FillColumnSet( $tiColumns, "X14", "Вводное устройство");
        $model->FillColumnSet( $tiColumns, "X15", "Диспетчеризация");
        $model->FillColumnSet( $tiColumns, "X16", "Двери шахты");
        $model->FillColumnSet( $tiColumns, "X17", "Двери кабины");
        $model->FillColumnSet( $tiColumns, "X18", "Электродвигатель");
        $model->FillColumnSet( $tiColumns, "X19", "Редуктор");
        $model->FillColumnSet( $tiColumns, "X20", "Тормоз");
        $model->FillColumnSet( $tiColumns, "X21", "Трансформатор");
        $model->FillColumnSet( $tiColumns, "X22", "Ограничитель скорости");
        $model->FillColumnSet( $tiColumns, "X23", "СП К");
        $model->FillColumnSet( $tiColumns, "X24", "КИУ");
        $model->FillColumnSet( $tiColumns, "X25", "Ловители кабины");
        $model->FillColumnSet( $tiColumns, "X26", "Кабина");
        $model->FillColumnSet( $tiColumns, "X27", "ЭМО");
        $model->FillColumnSet( $tiColumns, "X28", "Редуктор привода дверей");
        $model->FillColumnSet( $tiColumns, "X29", "Двигатель привода дверей");
        $model->FillColumnSet( $tiColumns, "X30", "Водило");
        $model->FillColumnSet( $tiColumns, "X31", "Приямок");
        $model->FillColumnSet( $tiColumns, "X32", "Противовес");
        $model->FillColumnSet( $tiColumns, "X33", "Направляющие");
        $model->FillColumnSet( $tiColumns, "X34", "Подвесной кабель");
        $model->FillColumnSet( $tiColumns, "X35", "Копоткое замыкание");
        $model->FillColumnSet( $tiColumns, "X36", "Обрыв проводов");
        $model->FillColumnSet( $tiColumns, "X37", "Ослабление клемм");
        $model->FillColumnSet( $tiColumns, "X38", "Хищения");
        $model->FillColumnSet( $tiColumns, "X40", "Ложные вызова и др.");
        $model->FillColumnSet( $tiColumns, "X60", "Причины не запуска лифта");
        

        echo GridView::widget([
    		'dataProvider' => $model->provider,
    		'columns' => $tiColumns, 
		]);

	?>

    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>
