<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;

$this->title = Yii::t('ticketinputform','Ticket input');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo Html::beginForm(['ticket-add'],'post'); 
      echo Html::hiddenInput('tiStatus', 'DISPATCHER_ASSIGN');
      echo Html::hiddenInput('DivisionType', 1);    //0-подразделение лифта 1-ЛАС 2-из списка
                                                    //3-подразделение ВДЭС  4-ЛАС ВДЭС 5-из списка ВДЭС

?>

<h1><?= Html::encode($this->title)?></h1> 

<div class="row">
  <div class="col-md-6">

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Source')); ?>
        </div>
        <div class="col-md-5">
          <?php echo Html::dropDownList('tiSource', 'null', ['ЦДС:Телефон'=>Yii::t('ticketinputform','Phone'), 'ЦДС:Письменное обращение'=>Yii::t('ticketinputform','Written appeal'),'ЦДС:1562'=>'1562'],  //2001,2002
                                                            ['id'=>'SourceSelect','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Object')); ?>
        </div>
        <div class="col-md-5">
          <?php echo Html::dropDownList('tiObject', 'null', ArrayHelper::map($model->tiObjects,'tiobjectcode','tiobject'),['id'=>'ObjectsSelect','class'=>'form-control','onChange'=>'onSelectObject()']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php //echo Html::label(Yii::t('ticketinputform','Problem')); ?>
          <?php echo Html::label("Причина<br>обращения"); ?>
        </div>
        <div class="col-md-8" id='divProblemSelect' onChange='onSelectProblem()'>
          <?php //echo Html::dropDownList('tiProblem', 'null', ArrayHelper::map($model->tiProblems,'tiproblemtypecode','tiproblemtypetext'),['id'=>'ProblemSelect','class'=>'form-control']); ?> 
          <?php echo $model->getProblemsList(1); ?>
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Details')); ?>
        </div>
        <div class="col-md-10">
          <?php echo Html::textarea('tiProblemDetails','',['id'=>'tiProblemDetailsInput','style'=>"resize: none",'class'=>'form-control', 'rows'=>1, 'maxlength' => 150]); ?> 
        </div>
      </div>

      <br>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Caller')); ?>
        </div>
        <div class="col-md-10">
          <?php echo Html::input('text','tiCaller','',['id'=>'ticallerInput','class'=>'form-control', 'placeholder'=>Yii::t('ticketinputform','Full name')]); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Phone')); ?>
        </div>
        <div class="col-md-6">
          <?php echo Html::input('text','tiCallerPhone','',['id'=>'tiCallerPhoneInput','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Addres')); ?>
        </div>
        <div class="col-md-10">
          <?php echo Html::input('text','tiCallerAddres','',['id'=>'tiCallerAddresInput','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Comment')); ?>
        </div>
        <div class="col-md-10">
          <?php echo Html::textarea('tiComment','',['id'=>'tiCommentInput','style'=>"resize: none",'class'=>'form-control', 'rows'=>5, 'maxlength' => 512]); ?> 
        </div>
      </div>

  </div>

  <div class="col-md-6">
      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Region')); ?>
        </div>
        <div class="col-md-8">
          <?php echo Html::dropDownList('tiRegion', 'null', ArrayHelper::map($model->tiRegions,'districtcode','districtname'), ['id'=>'tiRegionSelect','class'=>'form-control','onChange'=>'onSelectRegion()']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Street')); ?>
        </div>
        <div class="col-md-8">
          <?php 
          echo   Select2Widget::widget(
                [
                  'id' => 'tiStreetSelect',
                  'name' => 'tiStreet',
                  'settings' => [ 'width' => '100%' ],                 
                  'events' => [ 'select2:select' =>'onSelectStreet' ],
                ]
            );            
          ?>
          <?php// echo Html::dropDownList('tiStreet', 'null', ['улица1','улица2'], ['id'=>'tiStreetSelect','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Building')); ?>
        </div>
        <div class="col-md-3">
          <?php 
          echo   Select2Widget::widget(
                [
                  'name' => 'tiFacility',
                  'id' => 'tiFacilitySelect',
                  'settings' => [ 'width' => '100%' ],                 
                  'events' => [ 'select2:select' =>'onSelectFacility'],
                ]
            );            
          ?>
        </div>
      </div>

      <div class="row" id='divEntrance'>
        <div class="col-md-2" >
          <?php echo Html::label(Yii::t('ticketinputform','Entrance')); ?>
        </div>
        <div class="col-md-2" id='divEntranceInput'>
          <?php echo Html::input('text','tiEntrance','',['id'=>'tiEntranceInput','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row">
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Floor')); ?>
        </div>
        <div class="col-md-2">
          <?php echo Html::input('text','tiFloor','',['id'=>'tiFloorInput','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row" id='divElevatorSelectRow'>
        <div class="col-md-2" id='divElevatorSelectCaption'>
          <?php echo Html::label(Yii::t('ticketinputform','Elevator')); ?>
        </div>
        <div class="col-md-6" id='divElevatorSelect'>
          <?php // echo Html::dropDownList('tiElevator', 'null', ['улица1','улица2'], ['id'=>'tiElevatorSelect','class'=>'form-control']); ?> 
        </div>
      </div>

      <div class="row" id='divApartment'>
        <div class="col-md-2">
          <?php echo Html::label(Yii::t('ticketinputform','Apartment')); ?>
        </div>
        <div class="col-md-2">
          <?php echo Html::input('text','tiApartment','',['id'=>'tiApartmentInput','class'=>'form-control']); ?> 
        </div>
      </div>

  </div>


</div>
  <br>
      <div class="row">
        <div class="col-md-1">
          <?php echo Html::label(Yii::t('ticketinputform','Priority')); ?>
        </div>
        <div class="col-md-2">
          <?php echo Html::dropDownList('tiPriority', 
                                        'null', 
                                        [ 'NORMAL'=>Yii::$app->params['TicketPriority']['NORMAL'],
                                          'CONTROL1'=>Yii::$app->params['TicketPriority']['CONTROL1'],
                                          'CONTROL2'=>Yii::$app->params['TicketPriority']['CONTROL2'],
                                          'EMERGENCY'=>Yii::$app->params['TicketPriority']['EMERGENCY'],
                                        ],
                                        ['id'=>'PrioritySelect','class'=>'form-control','onChange'=>'onSelectPriority()']); 
          ?> 
        </div>
      </div>      

      <div class="row" >
        <div class="col-md-1">
          <?php echo Html::label(Yii::t('ticketinputform','Executant')); ?>
        </div>
        <div class="col-md-4" id="divExecutantLas">
          <?php 
             echo Select2Widget::widget(
                [
                  'id' => 'tiExecutantSelect',
                  'name' => 'tiExecutant',
                  'items'=> ArrayHelper::map($model->tiExecutantsLas, 'id','text'),
                  'settings' => [ 'width' => '100%' ],                 
                ]
          );      
          
          ?>
        </div>
        <div class="col-md-4" id="divExecutantDep">
        </div>
        <div class="col-md-4" id="divExecutantDepsList">
          <?php echo Html::dropDownList('tiDepSelect','null',ArrayHelper::map($model->tiDepsList,'id','divisionname'),['id'=>'tiDepSelect','class'=>'form-control']); ?>
        </div>

        <div class="col-md-4" id="divVDESExecutantLas">
          <?php 
             echo Select2Widget::widget(
                [
                  'id' => 'tiVDESExecutantSelectt',
                  'name' => 'tiVDESExecutant',
                  'items'=> ArrayHelper::map($model->getExecutantsListVDESForLAS(), 'id','text'),
                  'settings' => [ 'width' => '100%' ],                 
                ]
          );      
          ?>
        </div>
        <div class="col-md-4" id="divVDESExecutantDepsList">
          <?php echo Html::dropDownList('tiVDESDepSelect','null',ArrayHelper::map($model->getDivisionsListVDESForMaster(),'id','divisionname'),['id'=>'tiVDESDepSelect','class'=>'form-control','onChange'=>'OnSelectDep()']); ?>
        </div>
        <div class="col-md-4" id="divNoExecutantWarning" style="color:red;" hidden>
          Необходимо выбрать подразделение!
        </div>
      </div>

<!--
      <div class="row">
        <div class="col-md-1">
          <?php echo Html::label(Yii::t('ticketinputform','Comment')); ?>
        </div>
        <div class="col-md-8">
          <?php echo Html::textarea('tiComment2','',['id'=>'tiComment2Input','style'=>"resize: none",'class'=>'form-control', 'rows'=>5, 'maxlength' => 512]); ?> 
        </div>
      </div>
-->
<br>

<div class="row">
  <div class="col-md-offset-1">
    <?php echo Html::submitButton(Yii::t('ticketinputform','Transfer to LAS'),['id' => 'SubmitButton', 'class'=>'submit btn btn-success']); ?>
  </div>
</div>

<?php
echo Html::endForm();
?>

<?php
$addr1 = Url::toRoute(["get-streets-list"]);
$addr2 = Url::toRoute(["get-facility-list"]);
$addr3 = Url::toRoute(["get-problems-list"]);
$addr4 = Url::toRoute(["get-elevators-list"]);
$addr5 = Url::toRoute(["get-elevator-division"]);
$addr6 = Url::toRoute(["get-entrance-with-elevators"]);

$str1 = '"'.Yii::t('ticketinputform','Transfer to LAS').'"';
$str2 = '"'.Yii::t('ticketinputform','Transfer to mster').'"';
$str3 = "'".Html::input('text','tiEntrance','',['id'=>'tiEntranceInput','class'=>'form-control'])."'";

$strElCapElevator = Html::label(Yii::t('ticketinputform','Elevator'));
$strElCapPanel = Html::label(Yii::t('ticketinputform','Panel'));

$script = <<< JS

var DivId = null;

$(window).load(function () {
  onSelectRegion();
  onSelectStreet();
  CheckElevatorInputNeeded();
  CheckForLasNeeded();
});

function LoadExecutants(){

}

function CheckElevatorInputNeeded(){
  if ($("#ObjectsSelect").val() < 3){
    $("#divElevatorSelectRow").show();
    $("#divApartment").hide();
    //$("#divEntrance").hide();
    if ($("#ObjectsSelect").val() == 1){
      $("#divElevatorSelectCaption").html("$strElCapElevator");
    }else{
      $("#divElevatorSelectCaption").html("$strElCapPanel");
    }

    ElevatorSelectUpdate();
  }else{
    $("#divElevatorSelectRow").hide();
    $("#divApartment").show();
    //$("#divEntrance").show();
  }
}

function CheckForLasNeeded(){
  if (1 == $("#ObjectsSelect").val()) {
    // если объект - лифты
    $("#divVDESExecutantLas").hide();
    $("#divVDESExecutantDepsList").hide();
    if ( 1 == $("#ProblemSelect").val() ) {  
        $("#divExecutantLas").show();
        $( "input[name$='DivisionType']" ).val( 1 );
        $("#divExecutantDep").hide();
        $("#divExecutantDepsList").hide();
        $("#SubmitButton").html($str1);
    }else{
      var date = new Date();
      var hour = date.getHours() ;
      if ((hour<8) || (hour>16)){
          $("#divExecutantLas").show();
          $( "input[name$='DivisionType']" ).val( 1 );
          $("#divExecutantDep").hide();
          $("#divExecutantDepsList").hide();
          $("#SubmitButton").html($str1);
      }else{
          $("#divExecutantLas").hide();

          var ElevatorsNumber = document.getElementById("tiElevatorSelect").options.length;

          if (ElevatorsNumber>0){
            $( "input[name$='DivisionType']" ).val( 0 );
            $("#divExecutantDepsList").hide();
            $("#divExecutantDep").show();
          }else{
            $( "input[name$='DivisionType']" ).val( 2 );
            $("#divExecutantDepsList").show();
            $("#divExecutantDep").hide();
          }


          $("#SubmitButton").html($str2);
      }
    }
  }else{
    // если объект - не лифты
    $("#divExecutantDep").hide();
    $("#divExecutantDepsList").hide();
    $("#divExecutantLas").hide();
    //console.log($("#PrioritySelect").val());

    if ( $("#PrioritySelect").val() == 'EMERGENCY') {
      // срочная заявка
      $("#divVDESExecutantLas").show();
      $("#divVDESExecutantDepsList").hide();
      $("#SubmitButton").html($str1);
      $( "input[name$='DivisionType']" ).val( 4 );
      HideTransparencyNoExecutant();


    }else{
      $("#SubmitButton").html($str2);
      $("#divVDESExecutantLas").hide();        

      $("#divVDESExecutantDepsList").show();
      $( "input[name$='DivisionType']" ).val( 5 );

      DoSelectDep();
    /*
      var PanelsNumber = document.getElementById("tiElevatorSelect").options.length;
      if (PanelsNumber>0){
        $("#divVDESExecutantDepsList").hide();
        $("#divExecutantDep").show();
        $( "input[name$='DivisionType']" ).val( 3 );
      }else{
        $("#divVDESExecutantDepsList").show();
        $("#divExecutantDep").hide();
        $( "input[name$='DivisionType']" ).val( 5 );
      }
      */

    }   
      
  }

}

function onSelectPriority() {
  CheckForLasNeeded();
}

function onSelectStreet(){
    $.ajax({
         url: '$addr2',
         type: "POST",
         dataType: "json",
         data: {StreetId: $("#tiStreetSelect").val()},
         success: function(datamas) {
                $("#tiFacilitySelect").html("");
                $("#tiFacilitySelect").select2({data:datamas, width:'100%'});
                EntranceSelectUpdate();
         },
         error:   function() {
                $("#tiFacilitySelect").html('AJAX error!');
         }

  });
  return false;
}

function onSelectRegion(){
    $.ajax({
         url: '$addr1',
         type: "POST",
         dataType: "json",
         data: {District: $("#tiRegionSelect").val()},
         success: function(datamas) {
                $("#tiStreetSelect").html("");
                $("#tiStreetSelect").select2({data:datamas, width:'100%'});
                onSelectStreet();
         },
         error:   function() {
                $("#tiStreetSelect").html('AJAX error!');
         }

  });
  return false;
}

function onSelectProblem(){
    CheckForLasNeeded();
    return true;
}

function onSelectObject(){
    CheckElevatorInputNeeded();
    $.ajax({
         url: '$addr3',
         type: "POST",
         dataType: "json",
         data: {ObjectId: $("#ObjectsSelect").val()},
         success: function(data) {
              $("#divProblemSelect").html(data);
              onSelectProblem();
              CheckForLasNeeded();
              EntranceSelectUpdate();
         },
         error:   function() {
              $("#divProblemSelect").html('AJAX error!');
         }


    });
    CheckForLasNeeded();
    return false;
}

function ElevatorSelectUpdate(){
    $.ajax({
         url: '$addr4',
         type: "POST",
         dataType: "json",
         data: {
            FacilityId: $("#tiFacilitySelect").val(),
            EntranceId: $("#tiEntranceInput").val(),
            ObjectId: $("#ObjectsSelect").val()
         },
         success: function(datamas) {
                $("#divElevatorSelect").html(datamas['Elevators']);
                GetElDivision();
                CheckForLasNeeded();
         },
         error:   function() {
                $("#divElevatorSelect").html('AJAX error!');
         }

  });
}

// обновление списка подъездов в доме
function EntranceSelectUpdate(){
    if ('XXX' !== $("#ObjectsSelect").val())                  // просто убрал проверку
    {  
        $.ajax({
             url: '$addr6',
             data: {FacilityId: $("#tiFacilitySelect").val(), 
                    ObjectId: $("#ObjectsSelect").val()},
             success: function(datamas) {
                    $("#divEntranceInput").html(datamas);
                    ElevatorSelectUpdate();
             },
             error:   function() {
                    $("#divEntranceInput").html('AJAX error!');
             }

        });
    }else{
        $("#divEntranceInput").html($str3);
    }
}

function onSelectFacility(){
  EntranceSelectUpdate();
}

function onSelectEntrance(){
  ElevatorSelectUpdate();
}

function ShowTransparencyNoExecutant(){
  if (1 != $("#ObjectsSelect").val()) {
    if ( $("#PrioritySelect").val() != 'EMERGENCY') {
      $("#divNoExecutantWarning").show();
      $("#SubmitButton").attr('disabled', 'disabled');
    }
  }  
}

function HideTransparencyNoExecutant(){
    $("#divNoExecutantWarning").hide();
    $("#SubmitButton").removeAttr('disabled');
}

function OnSelectDep(){
  HideTransparencyNoExecutant();
  DivId = $("#tiVDESDepSelect").val();
}

// выбрать пункт в выпадающем списке выбора исполнителя
function DoSelectDep(){
  if (1 == $("#ObjectsSelect").val()) {
    // если объект - лифты
    $("#tiDepSelect").val(DivId)
    $("#divNoExecutantWarning").hide();
    $("#SubmitButton").removeAttr('disabled');
  }else{
    // если объект - не лифты
    $("#tiVDESDepSelect").val(DivId)
    if (DivId == null) {
      console.log("NoDivId");
      ShowTransparencyNoExecutant();
    }else{
      HideTransparencyNoExecutant();
    }
  }

}

function GetElDivision(){
    $.ajax({
         url: '$addr5',
         type: "POST",
         dataType: "json",
         data: {ElevatorId: $("#tiElevatorSelect").val(),
                ObjectId: $("#ObjectsSelect").val()},
         success: function(datamas) {
                $("#divExecutantDep").html(datamas['DivName']);
                DivId = datamas['DivId'];
                DoSelectDep();
         },
         error:   function() {
                $("#divExecutanDep").html('AJAX error!');
         }

  });
}

function onSelectElevator(){
  GetElDivision();
}



JS;

$this->registerJs($script, yii\web\View::POS_END);

?>



