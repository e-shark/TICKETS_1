<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;
use frontend\modules\employeeeq\models\EmployeeSearch;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Occupation;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model frontend\models\Division */

$this->title = $model->divisionname;//сокращенное название
$this->params['breadcrumbs'][] = ['label' => 'Список Подразделений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];

?>
<div class="division-view">
    <h1><?= Html::encode($this->title) ?></h1>
<h3>Карточка Подразделения</h3>

  <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?/* = DetailView::widget
     ([
        'model' => $model,
        'attributes' => 
        [
         //   'id',
            'divisionnameup',
            //'divisionfullname',
            [ 'attribute' => 'divisionemployee',
              'format' => 'raw',
              'value' => function($model) 
              { 
                return $model->getDivisionemployeer(); 
              } 
            ],
            'divisioncode',
            [ 'attribute' => 'divisioncodesvc',
               'value' => function($model) 
                    { return $model->getdivisionsvc();
                    }
            ],
                    //'divisioncodesvc'],
             [ 'attribute' => 'divisiondate',//выдаём - вместо null
               'value' => function($model) 
                    { return $model->getdivisiondate();
                    }
            ], 
           // 'divisiondate',
          // 'division_id',
          //  'divisioncompany_id',
        ],
     ]) 
    */?>
    <p>
        <? //= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

      <? $divisionname = Division::find()->orderBy('divisionname')->asArray()->all(); 
         // формируем массив с соответствующими id и divisionname
         $divisionnamelist = ArrayHelper::map($divisionname,'id', 'divisionname'); 
         // считываем все элементы БД Occupation с сотрировкой по occupationname
         $occupationname = Occupation::find()->orderBy('occupationname')->asArray()->all(); 
         // формируем массив с соответствующими id и occupationname
         $occupationnamelist = ArrayHelper::map($occupationname,'id', 'occupationname'); 
      ?>
    <A name=p></A>
    <h3>Сотрудники Подразделения <? echo ($model->divisionname); ?></h3>
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
              { //return $model->fullName;
                return Html::a ($model->fullName, $model->getUrl() ); 
              } 
            ],
            [ 'attribute' => 'occupation_id',//'ocname',//'occupation_id',
              'format' => 'raw',
              //'filter'=> $occupationnamelist,
              'filter' => Html::activeDropDownList($searchModel, 'occupation_id', 
                      ArrayHelper::map(Occupation::find()->orderBy('occupationname')->asArray()->all(),
                          'id', 'occupationname'),
                      ['class'=>'form-control','prompt' => 'Все']),

              'value' => function($model1) 
              { 
                return $model1->getOccupationname(); 
              } 

            ], 
            //'occupation_id',
            /*[ 'attribute' => 'division',//'Подразделения'
              'format' => 'raw',
              'filter' => $divisionnamelist,//Выдаёт выпадающий список с названиями
              'value' => function($model1) 
              {                 
                //$url=getUrl();
                return $model1->getDivisionname(); 
                //$this->render($url);
              } 
            ],*/ 
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
              //'filter' => ['1' => 'Работает', '0' => 'Уволен'],//Выдаёт выпадающий список с названиями
              'value' => function($model1) 
                {      
                if ('isemployed'=='0') {
                  Html::color('p(color=red)');
                  return $model1->getIsemployedname(); 
                  Html::color('');
                  } else {
                return $model1->getIsemployedname(); }
                }
            ], 
          ]
      ]);
  ?>
</div>
