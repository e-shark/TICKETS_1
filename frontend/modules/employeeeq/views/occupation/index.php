<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\OccupationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<center>
<?
//$this->title = 'Каталог';
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['site/index']];
$this->title = 'Список Должностей';
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];//$this->title;
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
</center>
<div class="occupation-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    //$districtTypes = OccupationSearch::find()->orderBy('streetdistrict')->asArray()->all(); 

   //  echo $this->render('_search', ['model' => $searchModel]);
   // ->orderBy('streetdistrict')
    ?>
    <p>
        <?= Html::a('Добавить Должность', ['create'], ['class' => 'btn btn-success'])   
        ?>
    </p>

    <?=GridView::widget
    ( 
       [
       'dataProvider' => $dataProvider,
     //  'filterModel' => $searchModel,//проба
       //  по-умолчанию: 'tableOptions' => ['class' => 'table table-striped table-bordered'],
       // 'filterModel' => $searchModel,
       // 'layout'=>'{emptyCell, 'Не назначено'}', 
       'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => 
        [
            ['class' => 'yii\grid\SerialColumn'
            ], 
            [ 'attribute' => 'occupationname',
              'format' => 'raw',
              'value' => function($model) 
              {
                return Html::a ($model->occupationname, $model->getUrl() );
              }
            //  }
            ], 
            [ 'attribute' => 'occupationcode',
               'value' => function($model) 
                {
                  return $model->getcode();
                }
            ], 
            [ 'attribute' => 'occupationemployee',
              'format' => 'raw',
              'value' => function($model) 
              { 
                return Html::a ($model->getOccupationemployeer(), $model->getUrlp() ); 
              } 
            ],
            
            //'occupationcode'
        ],
      ]
    )

//]);
?>
<?//= Html::a('Просмотр', ['view', 'id' => '1'], ['class' => 'profile-link']); ?>
    </p>

</div>