<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Employee */

$this->title = $model->fullname; //'Изменение данных' ;
$this->params['breadcrumbs'][] = ['label' => 'Справочник Персонала', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['', 'id' => $model->id]];

?>
<div class="employee-view">
    <? $isemployed=$model->isemployed; 
     if ($isemployed==1) 
        {
            $employment=$model->getemployment_date();
            $status='Работает c ';
            
        } else 
        {
            $employment=$model->getdismissal_date();
            $sex=$model->sex;
            if ($sex=='Ж') {
            $status='Уволена c ';
            } else { $status='Уволен c ';}
        }
        $fraza=$status.$employment;
    ?>
    <h1 align=center> <? if ($isemployed==1) { ?>
    <font color=green > <? } else { ?><font color=red > <?} ?>
    <?= Html::encode($this->title) ?> </h1>
 
      <p align=center> <font size=6> <b>
             <? echo ($fraza); ?></b>
        </font> </p> </font>

        <h3>Карточка Сотрудника</h3>
    <h2> <?= DetailView::widget
       ([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'attributes' => 
        [
        	'empcode',
            [ 'attribute' => 'occupation_id',
              'value' => function($model) 
                { 

                  return $model->getoccupationname(); }
            ],
            [ 'attribute' => 'division_id',
              'value' => function($model) 
                { return $model->getdivisionname(); }
            ],
            [ 'attribute' => 'oprights',
              'value' => function($model) 
                { return $model->getoprights(); }
            ],
            [ 'attribute' => $status,
               'value' =>function($model) 
                { 
                  if ($isemployed==1) 
                    { $employment=$model->getemployment_date(); } else 
                      { $employment=$model->getdismissal_date(); }
                 return $employment; 
                }
            ],
        ],
      ])
    ?> </h2>
</div>

<div class="employee-update">

    <h1><?//= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
