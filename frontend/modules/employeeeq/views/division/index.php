<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\db\Query;

$this->title = 'Список Подразделений';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
<div class="division-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить Подразделение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?
      $L=Лифты; $E=Электрощитовые; $S=Домофоны;
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> ",//элементов
        //'filterModel' => $searchModel,
       // 'sorter'=>['divisionname' => SORT_ASC], 
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'id',
            [ 'attribute' => 'divisionname',
              'format' => 'raw',
              'value' => function($model) 
              { 
                return Html::a ($model->divisionname, $model->getUrl() ); 
              } 
            ],
            // 'divisionname',
            'divisionfullname',
            [ 'attribute' => 'divisionemployee',
              'format' => 'raw',
              'value' => function($model) 
              { 
                return Html::a ($model->getDivisionemployeer(), $model->getUrlp() ); 
              } 
            ],
            //'divisioncode',
            [ 'attribute' => 'divisioncodesvc',
              'format' => 'raw',
              //'filter' => function($model) 
              // { 
              //   $model->getdivisionsvc()
              //   return $model->getdivisionsvc();//$model->divisioncodesvc;//divisionsvc;
              // },
              //       ['L' => $L,//'Лифты',
              //        'E' => $E,//'Электричество',
              //        'S' => $S,//'Домофоны',
              //       ], 
              'value' => function($model) 
              { 
                //$model->getdivisionsvc()
                return $model->getdivisionsvc_el();//$model->divisioncodesvc;//divisionsvc;
              } 
            ],
            //'divisioncodesvc',
            //'divisiondate',
            //'division_id',
            //'divisioncompany_id',
            //['class' => 'yii\grid\ActionColumn'],
        ],
        
    ]); ?>
</div>