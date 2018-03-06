<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Журнал экспорта в систему ИТЕРА';
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
                [
                    'label' =>"Отпр.",
                    'attribute' => 'isexportdone',
                    'content' => function($data){ return (($data['isexportdone']==1)?
                        '<span class="glyphicon glyphicon-ok" style="color:green"></span>':
                        '<span class="glyphicon glyphicon-remove" style="color:red"></span>').
                        (($data['txattempts']>1)?'<b style="color:red">'.$data['txattempts'].'</b>':'');
                    },
                ],
            	[
                    'label' =>"Время операции",
                    'attribute' => 'recordtime',
                    'content' => function($data){ return 
                        date("d-m-Y H:i:s",strtotime($data['recordtime'])).
                        (($data['isexportdone']==1)?'<br><b style="color:#006700">'.date("d-m-Y H:i:s",strtotime($data['txtime'])).'</b>':''); 
                    }
                    //'format'=>['date','dd-MM-yyyy  HH:m:s'],
                ],
                [
                    'label' =>"Номер",
                    'attribute' => 'ticode',
                    'content' => function($data){ 
                        $cremote = $data['ticode1562'];
                        $url = Url::toRoute(['tickets/view', 'id' => $data['ticket_id']]);
                        return  "<a href=$url>".$data['ticode'].'</a>'.($cremote?" <span class='glyphicon glyphicon-link' style='color:#E9967A;vertical-align:super;font-size:80%'></span><br><span style='font-weight:normal;font-size:11px;color:#E9967A'>$cremote</span>":'') ;},
                ],
                [
                    'label' =>"ФИО",
                    'attribute' => 'rperson',
                    'content' => function($data){ return '<b>'.$data['rperson']."</b>".     // This substituted person for Itera
                                "<br>".$data['person'];}    // This is a real person who did action
                ],
                [
                    'label' =>"Статус",
                    'attribute' => 'tistatuslogged',
                    'content' => function($data){ return 
                        "ИТЕРА:<b>".$data['rstatus_id'].'</b><br>'.Yii::$app->params['TicketStatus'][ $data['tistatuslogged' ]];
                    }
                ],
                [
                    'label' =>"Проблема",
                    'attribute' => 'oostypetext',
                    'content' => function($data){ return 
                        '<b>'.$data['roostypetext'].'</b><br>'.
                        ($data['oostypetext']?'':'<b style="color:red">(НЕ УСТАНОВЛЕНА)</b><br>').
                        str_replace("\n","<br>",$data['rdescription']);},
                    'contentOptions' => ['style' => 'min-width: 300px; white-space: normal;'],
                ],
                [
                    'label' =>"Даты заявки",
                    'attribute' => 'tiopenedtime',
                    'content' => function($data){ $dates=
                        'Создана&nbsp&nbsp&nbsp :'.$data['rcreated'].'<br>'.
                        'План. закр.:'.$data['rturnon_plan_time'];
                        if(!empty($data['rturnoff_time']))$dates.='<br><span style="color:red">'.
                        'Простой c&nbsp:'.$data['rturnoff_time'].'<br>'.
                        'Простой до:'.$data['rturnon_time'].'</span>';
                        return $dates;
                    }
                ],
                [
                    'label' =>"Tx",
                    'attribute' => 'txrequest',
                    'content' => function($data){ return str_replace("\n","<br>",$data['txrequest']);},
                    'contentOptions' => ['style' => 'font-size:10px;'],
                ],
                [
                    'label' =>"Rx",
                    'attribute' => 'txresult',
                    'content' => function($data){ return str_replace("\n","<br>",$data['txresult']);},
                    'contentOptions' => ['style' => 'font-size:10px; min-width:100px; white-space: normal;'],
                ],
            ]
		]);
	?>
    <p>*Проблема: 1-указанная Заявителем проблема (из справочника). 2-Комментарий Диспетчера.3-Уточненная проблема(или код 062).4-Неисправность, определенная персоналом (из справочника).5-Комментарий сотрудника </p>
    <input id="printButton" type="image" src="/img/print.png" value="Печать" onclick="print_page()"></input>
</div>