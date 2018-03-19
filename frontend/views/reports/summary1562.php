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
        $model->FillColumnSet( $tiColumns, "X60", "Прочие неисправности");
        

        echo GridView::widget([
    		'dataProvider' => $model->provider,
    		'columns' => $tiColumns, 
		]);

	?>

    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>
