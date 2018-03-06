<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use frontend\models\Tickets;
use yii\widgets\ActiveForm;


$this->title = 'Работа Аварийной Службы за '.$model->repmonth.'.'.$model->repyear;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//--- Format page for printing
$this->registerCss( '@media print { 
        h1,.wrap>.container{margin:0;padding:0;}
        .footer, .breadcrumb, .pagination, 
        div#chart_div,
        button#submitFltr1,input#printButton,   div#ditivexecutant,div#divtifindstr { display: none; } 
        .report-holder a[href]::after {content: "";}
        div.report-holder *:not(h1){font-size:10px}
}');
$this->registerJs( 'function print_page(){window.print() ;}', yii\web\View::POS_HEAD);
?>

<div class="report-holder">
    <h1><?= Html::encode($this->title) ?></h1>


  <?php echo $this->render('_paramsfilter1.php', [ 'model'=>$model]); ?>

  <?php //print_r($model->result);?>
  <?php  
    $columns=[
            [
            'label' =>"Район/День",
            'attribute' => 'districtname',
            'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            ],
            [
                'label' =>"Состояние",
                'attribute' => 'state'],
            [
            'label' =>"Итого",
            'attribute' => 'Total',
            'contentOptions'=>function($data){ return ['style'=>'font-weight: bold;'];},
            ]];
            //--- Add columns for days of month
            for( $i=1; $i <= $model->repdays; $i++ )array_push($columns,['attribute' => $i]);

    echo GridView::widget([
        'dataProvider' => $provider,
        'layout'=>"{sorter}\n{pager}\n{items}",
        'rowOptions'=> function ($model, $key, $index, $grid){
            if(($model['districtname']=='Итого') && ($model['state']=='Выполнено'))return ['style' => 'background-color:#799;font-weight: bold;'];
            if($model['districtname']=='Итого')return ['style' => 'background-color:#7799;font-weight: bold;'];
        },
        'columns' => $columns,
    ]);
    

    //---Here start drawing google charts...
    $this->registerJsFile('https://www.gstatic.com/charts/loader.js',['type'=>'text/javascript','position'=>yii\web\View::POS_HEAD]);
    //--Build columns array for google chart DataTable constructor
    $chartColumns[]=['type'=>'number', 'label'=>'Дни месяца']; // 1 column - for x-axis
    for($i=0;$i<count($model->result)-3;$i++) $chartColumns[] = ['type'=>'number', 'label'=>$model->result[$i]['districtname'].' '.$model->result[$i]['state']];
    $jchartDataTable=str_replace("'","\'",json_encode(['cols'=>$chartColumns],JSON_UNESCAPED_UNICODE)); // encode  json object for DataTable() constructor
    //--Build chart rows array for DataTable.addRows()
    for( $i=1; $i<=$model->repdays;$i++){
        $col = array_column($model->result,$i); // do the array transformation - get data columns for each date of month
        //array_pop($col);                        // drop  the 'totals' column
        array_pop($col);                        // drop  the 'totals' column
        array_pop($col);                        // drop  the 'totals' column
        array_pop($col);                        // drop  the 'totals' column
        $col = array_merge([$i],$col);          // Add days of month as 1 column
        $chartRows[]=$col;                      // assemble resulting 2-dimentional array of chart rows
    }
    $jchartRows = json_encode($chartRows,JSON_NUMERIC_CHECK);   // build json-encoded string for DataTable.addRows()
    //echo $jchartRows;
    
    // Add charts init code...
$script = <<< JS
      google.charts.load('current', {'packages':['corechart']});

      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var options = {
            'title':'$this->title',
            'width':1100,
            'height':500,
            vAxis: {
                title: 'Количество Заявок',
                format: '#'
            },
            hAxis: {
                title: 'Дни месяца',
                viewWindow:{max:$model->repdays}
            },
            //isStacked: true
        };
        var data = new google.visualization.DataTable($jchartDataTable);
        data.addRows($jchartRows);
        
        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
JS;
    $this->registerJs( $script,yii\web\View::POS_HEAD );

?>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>

</div>