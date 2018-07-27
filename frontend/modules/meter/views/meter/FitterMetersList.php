<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

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
                'label' => Yii::t('meter','Serial №'),
                'content' => function($data){
                	return "<a href=".Url::toRoute(['meter/meter-info']).'&MeterId='.$data['id'].' target="_blank">'.$data['meterserialno'].'</a>';
                }
            ],
            [
                'label' => Yii::t('meter','Type'),
                'attribute' => 'metermodel',
            ],
            [
                'label' => Yii::t('meter','Address'),
                'attribute' => 'addrstr',
            ],

            [
                'label' => Yii::t('meter','Date'),
                'attribute' => 'B_mtime',
            ],
            [
                'label' => Yii::t('meter','Readings')."<br>".Yii::t('meter','previous'),
                'encodeLabel' => false,
                'content' => function($data){
                    return "<a href=".Url::toRoute(['meter/enter-reading']).'&MeterId='.$data['id'].(is_null($data['B_mdata'])?' class="not-set"':'').' >'.(is_null($data['B_mdata'])?"(не задано)":$data['B_mdata']).'</a>';
                }
            ],

            [
                'label' => Yii::t('meter','Date'),
                'attribute' => 'A_mtime',
            ],
            [
                'label' => Yii::t('meter','Readings')."<br>".Yii::t('meter','current'),
                'encodeLabel' => false,
                'content' => function($data){
                    return "<a href=".Url::toRoute(['meter/enter-reading']).'&MeterId='.$data['id'].(is_null($data['A_mdata'])?' class="not-set"':'').' >'.(is_null($data['A_mdata'])?"(не задано)":$data['A_mdata']).'</a>';
                }
            ],

            [
                'label' => Yii::t('meter','Difference'),
                'attribute' => 'A_mtime',
                'content' => function($data){
                    if (!(is_null($data['A_mdata']) || is_null($data['A_mdata']))){
                        $res = $data['A_mdata'] - $data['B_mdata'];
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

    <?php echo Html::a(Yii::t('meter','Add meter'), Url::toRoute(['meter/meter-edit']), ['class' =>'submit btn btn-success']); ?>
</div>	

