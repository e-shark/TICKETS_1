<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\District;
use frontend\modules\employeeeq\models\Street;
use frontend\modules\employeeeq\models\Facility;
use conquer\select2\Select2Widget;
/* @var $this yii\web\View */
/* @var $model frontend\models\Elevator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="elevator-form">
  <?    $districtTypes = District::find()->where(['districtlocality_id' => 159])->orderBy('districtname')->asArray()->all(); 
    $districtTypeList = ArrayHelper::map($districtTypes, 'id', 'districtname'); 

    if ($model->district!=0) //Настройка фильтров в зависимости от наличия выбора
     {//район выбран
        $districtmodel=District::findOne($model->district);
        $districtname=$districtmodel->districtname; 
        $streetTypes = Street::find()->groupBy('streettype')
            ->where('streetdistrict = :districtname ', [':districtname'=>$districtname])
            ->asArray()->all(); 
        if ($model->streettype != '') 
          {//выбран район / выбран тип улицы
            $streetNames = Street::find()->orderBy('streetnameru')
              ->where('streetdistrict = :districtname ', [':districtname'=>$districtname])
              ->andwhere('streettype = :streettype',[':streettype'=>$model->streettype] )
              ->asArray()->all(); 
            if ($model->streetname!='')
             {//выбран район / выбран тип улицы / выбрана улица
              $idstreet = $model->streetid($model->streettype, $model->streetname);
              $fabuildingno = Facility::find()->orderBy('fabuildingno')
                ->where('fadistrict_id = :districtname ', [':districtname'=>$model->district])
                ->andwhere('fastreet_id = :idstreet',[':idstreet'=>$idstreet] )
                ->asArray()->all(); 
                goto fin;//выставили все фильтры, выход из насторойки фильтров
             } 
             else
              {//выбран район / выбран тип улицы / не выбрана улица
                $fabuildingno = Facility::find()->orderBy('fabuildingno')
                  ->where('fadistrict_id = :districtname ', [':districtname'=>$model->district])
                  ->asArray()->all(); 
                goto fin;//выставили все фильтры, выход из насторойки фильтров
              }
          } 
        else 
          {//район выбран / тип улицы не выбран 
          $streetNames = Street::find()->orderBy('streetnameru')
            ->where('streetdistrict = :districtname ', [':districtname'=>$districtname])
            ->asArray()->all();
          } //район выбран 
        $fabuildingno = Facility::find()->orderBy('fabuildingno')
            ->where('fadistrict_id = :districtname ', [':districtname'=>$model->district])
            ->asArray()->all(); 
     }
    else
    {//район не выбран
        $streetTypes = Street::find()->groupBy('streettype')->asArray()->all();
        if ($model->streettype != '') 
          {//район не выбран / выбран тип улицы
            $streetNames = Street::find()->orderBy('streetnameru')
              ->where('streettype = :streettype',[':streettype'=>$model->streettype] )
              ->asArray()->all(); 
            if ($model->streetname!='')
            {//район не выбран / выбран тип улицы / выбрана улица
              $idstreet = $model->streetid($model->streettype, $model->streetname);
              $fabuildingno = Facility::find()->orderBy('fabuildingno')
                ->where('fastreet_id = :idstreet',[':idstreet'=>$idstreet] )
                ->asArray()->all(); 
              goto fin;//выставили все фильтры, выход из насторойки фильтров
            } 
            else 
                { //район не выбран / выбран тип улицы / не выбрана улица
building:           $fabuildingno = Facility::find()->orderBy('fabuildingno')
                                    ->asArray()->all(); 
                    goto fin;//выставили все фильтры, выход из насторойки фильтров
                }
          } 
        else 
          {//район не выбран / тип улицы не выбран 
          $streetNames = Street::find()->orderBy('streetnameru')->asArray()->all();
          }
          goto building;
    }
fin:  
    $streetTypeList = ArrayHelper::map($streetTypes, 'streettype', 'streettype'); 
    $streetNameList = ArrayHelper::map($streetNames, 'streetnameru', 'streetnameru'); 
    $fabuildingList = ArrayHelper::map($fabuildingno, 'fabuildingno', 'fabuildingno'); //id
  ?>
  
  <?php $form = ActiveForm::begin(['action'=>['sendstreet','id'=>'id'], 'method'=>"post"]); ?>
    <h4> <b>
        <?=$form->field($model, 'district',// Район district
            [ 'template' => '<div class=col-md-2 >
            <label> <span>Район:</span>{input}</label>{error}</div>', ])
          ->dropDownList( $districtTypeList,
            [   'name'=>'district',
                'prompt'=>'Все',
                'onchange'=>'this.form.submit()'
            ] ) ?>

        <?=  $form->field($model,'streettype',//Тип улицы 
            [ 'template' => '<div class=col-md-2> 
              <label> <span>Тип улицы:</span>{input}</label>{error}</div>',
            ])
        ->dropDownList($streetTypeList, 
            ['prompt'=>'Все', 'name'=>'streettype', 'onchange'=>'this.form.submit()'] );  
        ?> 
        <?= $form->field($model, 'streetname',//Улица
            [ 'template' => '<div class=col-md-3> 
            <label> <span>Улица:</span>{input}</label>{error}</div>',
            ])->widget(Select2Widget::className(),
                [ 'options' => [
                    'name'=>'street',
                    'prompt'=>'Все',
                    'onchange'=>'this.form.submit()'
                    ],
                  'items'=>$streetNameList,
                ]
            ) ?>       

        <?= $form->field($model, 'house',//Дом
            [ 'template' => '<div class=col-md-2> 
            <label> <span>Дом:</span>{input}</label>{error}</div>',
            ])->widget(Select2Widget::className(),
                [ 'options' => [
                    'name'=>'house',
                    'prompt'=>'Все',
                    'onchange'=>'this.form.submit()'
                    ],
                  'items'=>$fabuildingList,
                ]
            ) ?>   
        <?= $form->field($model,'id')->hiddenInput(['name'=>'id'])->label(false)   //скрытое поле?>
    </b></h4><br>
        <center>
        <?//= Html::submitButton('Применить', ['class' => 'btn btn-success']) ?>

        <?//= Html::resetButton('Сбросить', ['class' => 'btn btn-primary', //не работает
          //'onclick'=>"/employeeeq/employee/emechanic','id'=>$model->id, 
          //                        'ElevatorSearch[elperson_id]'=>$model->id"]) ?>
          
        <?= Html::a('Сбросить', ['/employeeeq/employee/emechanic','id'=>$model->id, 
                                  'ElevatorSearch[elperson_id]'=>$model->id], 
                                ['class' => 'btn btn-primary']) ?>
        </center> 
         <br>
    <?php ActiveForm::end(); ?>

</div>
