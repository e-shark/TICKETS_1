<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use frontend\models\Elevator;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Employee */
$this->title = 'Добавить оборудование, закрепленное за электромехаником ';//$model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Список сотрудников подразделений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];

?>

<div class="employee-view">
    
    <h1 align=center>  <font color=green > 
    <?= Html::encode($this->title) ?> 
    </h1> </font>

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

<?  $model->district=$eldistrict; 
    $model->streettype=$elstreettype; 
    $model->streetname=$elstreetname; 
    $model->house=$elhouse;
    $model->emechanic=$emechanic;
?>

<div class="employee-update">

      <?= $this->render('_form2', [
        'model' => $model, 
    ]) ?>
    <? $id=$model->id; ?>
<?php $form = ActiveForm::begin(['action'=>['saver','id'=>$id], 'method'=>'post']); 
   // $query = Elevator::find()->where(['elperson_id' => '14']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'emptyText' => 'Результатов не найдено.',
        'summary' => "Показано <b>{begin} - {end}</b> из <b>{totalCount}</b> элементов",
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [  	'class' => 'yii\grid\CheckboxColumn' ],

            [   'attribute' =>'district', //'Район',
                'format' => 'raw',
            //     'filter'=> Html::activeDropDownList($searchModel, 'district', $districtTypeList, 
              //      ['class'=>'form-control','prompt' => 'Все']),
                'value'=>function ($model){
                  return $model->districtname; },
            ],
            [
                'format' => 'raw',
                'label'  => 'Улица',
                   'value'=>function ($model){
                return $model->streetname; },
            ],
            [
                'format' => 'raw',
                'label'  => 'Дом',
                'value'=>function ($model){
                  return $model->faaddressno; },
            ],
            'elporchno',    //'label'  => 'Подъезд',
            [
                'attribute' =>'eltype',//Наименование оборудования
                'format' => 'raw',
                //'label'  => 'Наименование оборудования',
                'value'=>function ($model){
                  return $model->eltypel; },
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
            [   'attribute' =>'elperson_id', //'label'  =>'Электромеханик',
                'format' => 'raw',
                'value'=>function ($model){
                  return Html::a ($model->elpersonname, $model->getUrlelp());},
            ], 
        ],
    ]); ?>
<p>
 <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
 
<?php ActiveForm::end(); ?>
 <?= Html::a('К списку оборудования, закрепленного за подразделением', $model->urldiv, ['class' => 'btn btn-primary']) ?>
 </p>
</div>