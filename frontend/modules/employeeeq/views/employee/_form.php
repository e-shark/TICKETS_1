<?php
header('Content-Type: text/html; charset=utf-8');
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Occupation;
use frontend\modules\employeeeq\models\User;
//use phpnt\datepicker\BootstrapDatepicker;


/* @var $this yii\web\View */
/* @var $model frontend\models\Employee */
/* @var $form yii\widgets\ActiveForm */

?>
        <? $isemployed= $model->isemployed;//isemployed;
            if ($isemployed!=0) {
            $model->isemployed=1;//'ghjhghj';//isemployed='1'; //isemployed = '1';  //выставляем значение 1
        } 
            ?>

<div class="employee-form">

    <h3>Данные сотрудника</h3>

    <?php $form = ActiveForm::begin(); ?>

    <?//= $form->field($model, 'remoteid')->textInput() ?>

    <?= $form->field($model, 'empcode')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['style'=>'width:300px','maxlength' => true])//->hint('Обязательное к заполнению поле') ?>

    <?= $form->field($model, 'firstname')->textInput(['style'=>'width:300px','maxlength' => true])?>

    <?= $form->field($model, 'patronymic')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->radioList(['M' => 'Мужской', 'Ж' => 'Женский']);//, '' => 'Не выбрано']); ?>

    <?=$form->field($model, 'employmentdate')->// Д А Т А  П Р И Ё М А   Н А  Р А Б О Т У
        widget(Datepicker::className(),
        [   'language'              => 'ru-RU',
            'value'  => $model->employmentdate,
            'dateFormat' => 'yyyy-MM-dd',
        // 'options'=>['class'=>'form-control'],
        ]);  ?>

     <? if ($isemployed=='0')  {   // Д А Т А   У В О Л Ь Н Е Н И Я
     ?>   
    <?= $form->field($model, 'dismissaldate')->widget(Datepicker::className(),
        [ 'language'              => 'ru-RU',
          'value'  => $model->dismissaldate,
          'dateFormat' => 'yyyy-MM-dd',
       // 'options'=>['class'=>'form-control'],
    ]); ?>
     <? } ?>

 <?=  $form->field($model, 'division_id',// П О Д Р А З Д Е Л Е Н И Е
        [
            'template' => '<div class=mclass2> 
            <label> <span>Подразделение</span>{input}</label>{error}</div>',
        ]
        )->dropDownList( //[''=>'Не выбрано',
            ArrayHelper::map(
                Division::find()->all(), 'id', 'divisionname'), //],
            [

                $model->division_id => ['selected' => true],
                'maxlength' => true,
                'options' => array('AT'=>array('selected'=>true)),
                'prompt'=>'Не выбрано',
            ]
        ) ?>

    <?=  $form->field($model, 'occupation_id', // Д О Л Ж Н О С Т Ь
        [
           'template' => '<div class=mclass2> 
            <label> <span>Должность
            </span>{input}</label>{error}</div>',
        ]
        )->dropDownList( 
            ArrayHelper::map(
                Occupation::find()->all(), 'id', 'occupationname'),
            [
              //  $model->occupation_id => ['selected' =>true],
              //  'maxlength' => true,
              //  'options' => array('AT'=>array('selected'=>true)),
                'prompt'=>'Не выбрано',
            ]    
        )
    ?>
   
    <?= $form->field($model, 'oprights') // О П Е Р А Т И В Н Ы Е   П Р А В А
        ->dropDownList([
        //''=>'Не выбрано',
        'D'=>'Диспетчер',
        'M'=>'Мастер',
        'd'=>'Оператор',
        'F'=>'Электромеханик',
        ], ['style'=>'width:300px', 'maxlength' => true, 'prompt'=>'Не выбрано'] ); //, 'multiple' => true
    ?>
    <?= $form->field($model, 'education')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'workphone')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'workmail:email')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>
    <?$employees = Employee::find()->orderBy('lastname')->asArray()->all(); 
    ?>
    <?=  $form->field($model, 'user_id',// П О Л Ь З О В А Т Е Л Ь
        [
            'template' => '<div class=mclass2> 
            <label><span>Имя Пользователя Системы</span>
            {input}</label>{error}</div>',
        ]
        )->dropDownList( 
            User::find()->where( 
                   ['OR', 
                    ['not in', 'id', Employee::find()->select(["(ifnull (user_id,0))"])],
                    ['id' => $model->user_id] ]
                   )->orderBy('username')
                   ->select(['username', 'id'])->indexBy('id')
                   ->column(),
                   ['prompt'=>'Не выбрано']
        ) 
    ?>

    <h3>Персональные данные сотрудника</h3>

    <?//= $form->field($model, 'birthday')->textInput(['style'=>'width:300px']) ?>
    <?= $form->field($model, 'birthday')->widget(Datepicker::className(),
        [ 'language'              => 'ru-RU',
          'value'  => $model->birthday,
          'dateFormat' => 'yyyy-MM-dd',
       // 'options'=>['class'=>'form-control'],
    ]); ?>
    
    <?= $form->field($model, 'passportno')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'passportdata')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'personcode')->textInput(['style'=>'width:300px','maxlength' => true])?>

    <?= $form->field($model, 'personaddress')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'currentaddress')->textInput(['style'=>'width:300px','maxlength' => true]) ?>

    <?= $form->field($model, 'personphone')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'personphone1')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?= $form->field($model, 'personemail')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>


    <?//= $form->field($model, 'isemployed')->radioList(['1' => 'Работает', '0' => 'Уволен']) ?>
    
     <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        
        <?= Html::a('К Списку Сотрудников', ['index'], ['class' => 'btn btn-primary']) ?>
    
    <?if ($isemployed==1) { ?>
        <?= Html::a('Уволить', ['dismissal', 'id' => $model->id], ['class' => 'btn btn-danger', 
                'data' => [
                'confirm' => 'Вы уверены, что хотите уволить этого Сотрудника?',
                'method' => 'post',
            ], ]);
         ?>
    <? } else { ?> 
            <?= Html::a('Устроить', ['missal', 'id' => $model->id], ['class' => 'btn btn-success',
                'data' => [
                //'confirm' => 'Устроить этого Сотрудника?',
                'method' => 'post',
            ], ]);
            ?>
    <? } ?>   
    <? if ($model->occupation_id==3) { 
        echo "<br> <br>";
       if ($model->elevatorscount!=0)//есть оборудование 
            {   
                // $url=Url::toRoute(['emechanic','ElevatorSearch[elperson_id]'=>
                //     $this->id,'id'=>$this->id]);
                echo Html::a('Список оборудования, закрепленного за электромехаником',
                    $model->urlelp, ['class' => 'btn btn-primary']);
            }
        else {
                echo Html::a('Добавить оборудование, закрепленное за электромехаником',
                    $model->urlelp, ['class' => 'btn btn-primary']);
            }
    } ?>
    </div>
    
 
    <?php ActiveForm::end(); ?>
   
</div>
    <?// <a name="data"></a>= $form->field($model, 'postcode')->textInput(['style'=>'width:300px']) ?>

    <?//= $form->field($model, 'personurl')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'sex')-> textInput(['maxlength' => true]) ?>
    
    <?//= $form->field($model, 'sex')->radioList(['M' => 'Мужской', 'Ж' => 'Женский']);//, '' => 'Не выбрано']); ?>
    
    <?//= $form->field($model, 'birthday')->textInput(['style'=>'width:300px']) ?>

    <?//= $form->field($model, 'married')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'salary')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'rate')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'skillscategory')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'skillsrank')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'certprofessional')->textInput(['style'=>'width:300px']) ?>

    <?//= $form->field($model, 'certmedical')->textInput(['style'=>'width:300px']) ?>

    <?//= $form->field($model, 'certnarcology')->textInput(['style'=>'width:300px',]) ?>

    <?//= $form->field($model, 'certpsych')->textInput(['style'=>'width:300px',]) ?>

    <?//= $form->field($model, 'certcriminal')->textInput(['style'=>'width:300px',]) ?>

    <?//= $form->field($model, 'statusmilitary')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?//= $form->field($model, 'statusdisability')->textInput(['style'=>'width:300px']) ?>

    <?//= $form->field($model, 'statuschernobyl')->textInput(['style'=>'width:300px',]) ?>

    <?//= $form->field($model, 'lastjob')->textInput(['style'=>'width:300px', 'maxlength' => true]) ?>

    <?/*= $form->field($model, 'employmenttype')//->textInput(['style'=>'width:300px', 'maxlength' => true])
    ->dropDownList( ['FT' => 'Полная Занятость', 'PT' => 'Частичная Занятость', 
        'UFT' => 'Полная Занятость без регистрации',
        'UPT' => 'Частичная Занятость без регистрации'],['style'=>'width:300px', 'maxlength' => true]); 
    */?>

    <?//= $form->field($model, 'user_id')->textInput() ?>