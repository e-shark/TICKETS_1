<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use himiklab\colorbox\Colorbox;

$this->title = Yii::t('meter','Fitter Meter list');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-holder">

	<h1><?= Html::encode($this->title) ?></h1>
    <div>
        <?php echo $this->render('_metersparamsfilter.php', [ 'model'=>$model]); ?>
    </div>
   	<?php   
        $mtrColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('meter','Meter'),
                'content' => function($data){
                    $res = "<a href=".Url::toRoute(['meter/meter-info']).'&MeterId='.$data['id'].' target="_blank">'.$data['meterserialno'].'</a>';
                    $res .= "<br><span style='font-weight:normal;font-size:10px;'>".($data['metermodel'])."</sran>";
                    return $res;
                }
            ],
            [
                'label' => Yii::t('meter','Address'),
                'attribute' => 'addrstr',
            ],

            [
                'label' => Yii::t('meter','Readings')."<br>".Yii::t('meter','previous'),
                'encodeLabel' => false,
                'content' => function($data){
                    if ( is_null($data['C_mtime']) ) {
                        $res ='<span class="not-set" >'."(не задано)"."</spam>";
                    }else{
                        $ts = strtotime($data['C_mtime']);
                        $_date = date("Y-m-d",$ts);
                        $_time = date("H:i:s",$ts);
                        $res = '<div style=" display:inline-block">'.$_date.'</div>  ';
                        $res .= "<div style='float:right; display:inline-block'><b>".$data['C_mdata']."</b></div>";
                        $res .= '<br>';
                        $res .= "<span style='font-weight:normal;font-size:10px;'>".$_time."</span>";
                    }
                    return $res;
                }

            ],
            [
                'label' => Yii::t('meter','Readings')."<br>".Yii::t('meter','current'),
                'encodeLabel' => false,
                'content' => function($data){
                    if ( is_null($data['A_mtime']) ) {
                        $res ="<a href=".Url::toRoute(['meter/enter-reading']).'&MeterId='.$data['id']." class='not-set' >"."(не задано)"."</a>";
                    }else{
                        $ts = strtotime($data['A_mtime']);
                        $_date = date("Y-m-d",$ts);
                        $_time = date("H:i:s",$ts);
                        $res .= '<div style=" display:inline-block">'.$_date.'</div>  ';
                        $res .= "<div style='float:right; display:inline-block'><a href='".Url::toRoute(['meter/enter-reading']).'&MeterId='.$data['id']."'><b>".$data['A_mdata']."</b></a></div>";
                        $res .= '<br>';
                        $res .= "<span style='font-weight:normal;font-size:10px;'>".$_time."</span>";
                    }

                    return $res;
                }

            ],

            [
                'contentOptions' =>function ($model, $key, $index, $column){ return ['style' => 'text-align:  center;']; },
                'content' => function($data){
                    if (!empty( $data['A_mfile']))
                        $res = "<a class='meterdataphoto' href=".Url::toRoute(['meter/get-meter-photo','MeterId' => $data['id'], 'ReadingId'=>$data['A_tid'], 'type'=>'.jpeg']).'><img src="/img/camera_small.png" alt="MDN"></a>'; //.' target="_blank"
                    else
                        $res = "";
                    return $res;
                }
            ],

/*
            [
                'contentOptions' =>function ($model, $key, $index, $column){ return ['style' => 'text-align:  center;']; },
                'content' => function($data) {
                        return Html::a(
                            //'<span class="glyphicon glyphicon-trash" style="color: red;">',
                            '<span class="glyphicon glyphicon-remove" style="color: red;"></span>',
                            Url::toRoute(['meter/delete-all-current', 'MeterId'=>$data['id'] ]),
                            ['data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?')]
                        );
                    }       
            ],
*/            

            [
                //'label' => "&Delta;",
                'label' => "<div><span style='text-align: center;'>"."&Delta;"."<span></div>",
                'headerOptions' =>function ($model, $key, $index, $column){ return ['style' => 'text-align: center']; },
 
                'encodeLabel' => false,
                'contentOptions' =>function ($model, $key, $index, $column){ return ['style' => 'text-align:  center;']; },
                'content' => function($data){
                    if (!(is_null($data['C_mdata']) || is_null($data['A_mdata']))){
                        $res = $data['A_mdata'] - $data['C_mdata'];
                    }else{ $res = "-"; }
                    return $res;
                }

            ],



        ];

        echo GridView::widget([
    		'dataProvider' => $provider,
    		'columns' => $mtrColumns, 
		]);
	?>

</div>	

<?= Colorbox::widget([
    'targets' => [
        '.meterdataphoto' => [
            'maxWidth' => 1000,
            'maxHeight' => 700,
            'opacity' => 0.6,
        ],
    ],
    'coreStyle' => 4
]) ?>
