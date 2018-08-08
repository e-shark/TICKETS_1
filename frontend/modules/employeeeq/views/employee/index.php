<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Occupation;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use conquer\select2\Select2Widget;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список сотрудников подразделений';
//$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['']];
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
    ?>

    <p>
        <?= Html::a('Добавить сотрудника', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
             [ 'attribute' => 'empcode',//Табельный Номер
              'format' => 'raw',
              'value' => //'empcode',
              function($model1) 
              { 
                return $model1->getempcode();
              } 
            ],
            [ 'attribute' => 'division_id',//'Подразделения'
              'format' => 'raw',
              'filter' => Html::activeDropDownList($searchModel, 'division_id', 
                      ArrayHelper::map(Division::find()->orderBy('divisionname')->asArray()->all(), 
                            'id', 'divisionname'),
                      ['class'=>'form-control','prompt' => 'Все']),
              'value' => function($model1) 
              {                 
                return $model1->getDivisionname(); 
              } 
            ], 
            [ 'attribute' => 'fullName',//Фамилия Имя Отчество
              'format' => 'raw',
              'value' => function($model) 
              { 
                return Html::a ($model->fullName, $model->getUrl() ); 
              } 
            ], 
            
            [ 'attribute' => 'occupation_id',//Должность
              'format' => 'raw',
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
            [ 'attribute' => 'oprights',//Оп-Права
              'format' => 'raw',
              'filter'=> Html::activeDropDownList( $searchModel, 'oprights',
                    [
                     'D' => 'Диспетчер',
                     'M' => 'Мастер',
                     'F' => 'Електромеханик',
                     'd' => 'Оператор',
                    ], ['class'=>'form-control','prompt' => 'Все']
                    ),
              'value' => function($model1) 
              { 
                return $model1->getoprights_el();
              }
            ],
            [ 'attribute' => 'isemployed',//Статус Работает/Уволен
              'format' => 'raw',
              'filter' => Html::activeDropDownList( $searchModel, 'isemployed',
              ['1' => 'Работают', '0' => 'Уволены'],
              ['class'=>'form-control','prompt' => 'Все'] ),
              'value' => function($model1) 
              {                 
                return $model1->getIsemployedname(); 
              } 
            ],
           // ['class' => 'yii\grid\ActionColumn','template' => '{view} {update}'],//'header'=>'Действия'
        ],
    ]); 
     ?>
</div>
