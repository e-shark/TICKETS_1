<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use frontend\modules\employeeeq\models\EmployeeSearch;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Occupation;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model frontend\models\Occupation */

$this->title = $model->occupationname;
//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Справочник Должностей', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="occupation-view">

    <h1><?= Html::encode($this->title) ?>
    
    <? $a=$model->occupationcode;
       if ($a!=0) {
          echo (' ('.$a.')');
        } ?>
    </h1>
        <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
        <?
        // считываем все элементы БД Division с сотрировкой по divisionname
      $divisionname = Division::find()->orderBy('divisionname')->asArray()->all(); 
      // формируем массив с соответствующими id и divisionname
      $divisionnamelist = ArrayHelper::map($divisionname,'id', 'divisionname'); 
      
      // считываем все элементы БД Occupation с сотрировкой по occupationname
      $occupationname = Occupation::find()->orderBy('occupationname')->asArray()->all(); 
      // формируем массив с соответствующими id и occupationname
      $occupationnamelist = ArrayHelper::map($occupationname,'id', 'occupationname'); 

      $id=$model->id ?>
    <A name=p></A>
     <h3>Сотрудники на Должности: <? echo ($model->occupationname); ?></h3>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [ 'attribute' => 'fullName',
              'format' => 'raw',
              'value' => function($model) 
              { 
                return Html::a ($model->fullName, $model->getUrl() ); 
              } 
            ],
            //'occupation_id',
            [ 'attribute' => 'division',//'Подразделения'
              'format' => 'raw',
              'filter' => Html::activeDropDownList($searchModel, 'division', 
                      ArrayHelper::map(Division::find()->orderBy('divisionname')->asArray()->all(), 
                            'id', 'divisionname'),
                      ['class'=>'form-control','prompt' => 'Все']),
              //'filter' => $divisionnamelist,//Выдаёт выпадающий список с названиями
              'value' => function($model1) 
              {                 
                return $model1->getDivisionname(); 
              } 
            ],
            [ 'attribute' => 'empcode',//Табельный Номер
              'format' => 'raw',
              'value' => //'empcode',
              function($model1) 
              { 
                return $model1->getEmpcode();             
              } 
            ],

            [ 'attribute' => 'isemployed',//Статус Работает/Уволен
              'format' => 'raw',
              'filter' => Html::activeDropDownList( $searchModel, 'isemployed',
              ['1' => 'Работает', '0' => 'Уволен'],
              ['class'=>'form-control','prompt' => 'Все'] ),
              'value' => function($model1) 
              {                 
                return $model1->getIsemployedname(); 
              } 
            ], 
          ]
      ]);
?>











    <?/*= DetailView::widget([
        'model' => $model,
        'attributes' => [
        //    'id',
          
            'occupationname',
            'occupationcode',
            [ 'attribute' => 'occupationname',
               'value' => function($model) 
                {
                  //$id='id';
                  //return $idocc;
                  return $model->getXr();
                } 
            ],
        ],
    ]) ?>
      <?/*
//        Worker::find()->where(['j.id' => 10])->joinWith('jobs j')->all();
//      $dataProvider1=
      ?>
        <?/*=GridView::widget
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
               'value' => function($model) 
                {
                  $id='id';
                  //return $idocc;
                  return $model->occupationname;
                } 
            ],
            ['attribute' => 'employee_oc', //Html::a(['view', 'id' => '1']),
              'format' => 'raw',
              'value' => function($model) 
              {
                $id=$model->id;
                return Html::a ($model->employee_oc, $model->getUrlempl() ); 
                //Html::a ($model->employee_oc, $model->getUrl() );//ParentName() );
                //return $id;
              }
            ],
            [ 'attribute' => 'occupationname',
               'value' => function($model) 
                {
                  //$id='id';
                  //return $idocc;
                  return $model->getXr();
                } 
            ],
             //'occupationname', //Html::a(['view', 'id' => '1']),
            
            /*[ 'attribute' => 'occupationname',
               'value' => function($model) 
                {
                  return array($model->getActorNames() );
                }
            ], 
            //'occupationcode'
            //'employee_oc'=>'Фамилия Имя Отчество',
            //'division_oc'=>'Название Подразделения'
        ],
      ]
    ) 
*/
//]);
 ?>
</div>
