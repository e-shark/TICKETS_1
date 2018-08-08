<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use frontend\modules\employeeeq\models\Elevator;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Street;
use frontend\modules\employeeeq\models\District;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */ 
/* @var $searchModel frontend\models\ElevatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Каталог';
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['default/index']];
$this->title ='Закреплённое оборудование по подразделениям';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index', 'id' => $model->id]];

//$this->title = 'Elevators';
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="elevator-index">

    <?
    $districtTypes = Street::find()->orderBy('streetdistrict')->asArray()->all(); 
    $districtTypeList = ArrayHelper::map($districtTypes, 'streetdistrict', 'streetdistrict'); ?>

    <h1><?= Html::encode($this->title) ?></h1>
    
     <div class="form-group">
      <?= $this->render('_form1', ['model' => $model]) ?>
    </div>
    <? $query = Elevator::find()->where(['eldivision_id' => $id]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

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
                  },
            ],            //'elremoteid',
            //'elperson_id',
            [   'attribute' =>'elperson_id', //'label'  =>'Электромеханик',
                'format' => 'raw',
                'value'=>function ($model){
                return Html::a ($model->elpersonname, $model->getUrlelp() ); 
                  },
            ],
        ],
    ]); ?>
</div>
