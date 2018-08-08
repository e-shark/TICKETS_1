<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use frontend\models\Elevator;

/* @var $this yii\web\View */
/* @var $model frontend\models\Employee */
$this->title = 'Список оборудования, закрепленного за электромехаником';//$model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Список сотрудников подразделений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];

?>
<div class="employee-view">
    
    <h1 align=center> 
    <font color=green > 
    <?= Html::encode($this->title) ?> </h1>
 
      <p align=center> <font size=6> <b>
             <?//echo $model->getdivisionname();// ($fraza);
              ?></b>
        </font> </p> </font>

    <h2> <?= DetailView::widget
       ([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'attributes' => 
        [
            'fullName',
            [ 'attribute' => 'division_id',
              'format' => 'raw',
              'value' => function($model) 
                { return Html::a ($model->divisionname, $model->Urldelp ); }
            ],
            [ 'attribute' => 'Всего Оборудования:',
               'value' => $model->elevatorscount,
            ],
        ],
      ])
    ?> </h2>
</div>
<?= Html::a('Добавить оборудование', ['append','id' => $model->id],// 'ElevatorSearch[elperson_id]'=>$model->id],
      ['class' => 'btn btn-success', 
                'data' => [
                //'confirm' => 'Вы уверены, что  хотите уволить этого Сотрудника?',
                'method' => 'post',
            ], ]); ?>
<div class="employee-update">
<?  $model->district=$eldistrict; 
    $model->streettype=$elstreettype; 
    $model->streetname=$elstreetname; 
    $model->house=$elhouse; ?>
<br> <br>
      <?= $this->render('_form1', [
        'model' => $model,
    ]) ?>

 <?// $query = Elevator::find()->where(['elperson_id' => '14']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'id',
            [   'attribute' =>'district', //'Район',
                
                'format' => 'raw',
              //     'filter'=> Html::activeDropDownList($searchModel, 'district', $districtTypeList, 
              //      ['class'=>'form-control','prompt' => 'Все']),
                'value'=>function ($model){
                return $model->districtname;
                  },
            ],
            [
                'format' => 'raw',
                'label'  => 'Улица',
                   'value'=>function ($model){
                return $model->streetname;
                  },
            ],
            [
                'format' => 'raw',
                'label'  => 'Дом',
                   'value'=>function ($model){
                return $model->faaddressno;
                  },
            ],
            'elporchno',    //'label'  => 'Подъезд',
            [
                'attribute' =>'eltype',//Наименование оборудования
                'format' => 'raw',
                //'label'  => 'Наименование оборудования',
                   'value'=>function ($model){
                return $model->eltypel;
                  },
            ],
            //'eltype',//Наименование оборудования
            //'elinventoryno',//Инвентарный номер
            [
                'attribute' =>'elinventoryno',//Инвентарный номер
                'format' => 'raw',
                //'label'  => 'Инвентарный номер',
                'value'=>function ($model){//ссылка на паспорт оборудования
                  return Html::a ($model->elinventoryno, $model->urlinventoryno);
                //return $model->elinventoryno;
                  },
            ],            //'elremoteid',
            /* [   'attribute' =>'elperson_id', //'label'  =>'Электромеханик',
                'format' => 'raw',
                'value'=>function ($model){
                return Html::a ($model->elpersonname, $model->getUrlelp() ); // переход временно на карточку сотрудника
                  },
            ], */
        ],
    ]); ?>
  <?= Html::a('К списку оборудования, закрепленного за подразделением', $model->urldiv, ['class' => 'btn btn-primary']) ?>
</div>
