<?php  
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use frontend\modules\meter\models\MetersList;
use frontend\models\Tickets;
use yii\helpers\ArrayHelper;
use conquer\select2\Select2Widget;
use frontend\models\TicketInputForm;


$oprights=Tickets::getUserOpRights();
  //--- Filtering panel1: District,Calltype,Datefrom,Dateto
  echo "<div id='meterparamsfilter'>";
      echo '<p>';

      //----Filter all button
      echo Html::beginForm(['/'.$this->context->getRoute()],'get',['class'=>'form','id'=>'MeterFiltr']);
      echo '<div class="row">';

      if( FALSE === strpos($oprights['oprights'],'F' ) ) {
        // это не механик
        //---- Выбор механика
        if( array_key_exists('fitter',$model->attributes ) ) {
          $fitters = TicketInputForm::getFittersWithSBList();
          //$fitters = [["text"=>'Все','id'=>0]]+$fitters;
          array_unshift($fitters,["text"=>'Все','id'=>0]);
          echo '<div class="form-group col-xs-3"> Электромонтер :';
          echo   Select2Widget::widget([
            'id' => 'fitter',
            'name' => 'fitter',
            'settings' => [ 'width' => '100%', 'val' => "611" ],                 
            'items' => ArrayHelper::map($fitters,'id','text'),
            'value' => $model->fitter,
          ]);           
          echo '</div>';
        }
      }else{
        // это механик
        //---- Галочка  "только закрепленные"
        if( array_key_exists('assigned',$model->attributes ) ) {
          echo '<div class="form-group col-xs-2"> Закрепленные :';
          echo Html::checkbox('assigned', $model->assigned,['id'=>'assigned','class'=>'form-control']);
          echo '</div>';
        }
      }

      //---- Список районов
      if( array_key_exists('district',$model->attributes ) ) {
        $districts = TicketInputForm::getTiRegions();
        // $districts = [['districtname'=>'Все','districtcode'=>0]] + $districts;
        array_unshift($districts, ['districtname'=>'Все','districtcode'=>0]);
        echo '<div class="form-group col-xs-2"> Район :';
        echo Html::dropDownList('district', $model->district, ArrayHelper::map($districts,'districtcode','districtname'), ['id'=>'district','class'=>'form-control','onChange'=>'onSelectRegion()']); 
        echo '</div>';
      }

      //---- Список улиц
      if( array_key_exists('street',$model->attributes ) ) {
        $streets = ArrayHelper::map( TicketInputForm::getStreetsList( $model->district, true),'id','text' );
        echo '<div class="form-group col-xs-3"> Улица :';
        echo   Select2Widget::widget([
          'id' => 'street',
          'name' => 'street',
          'settings' => [ 'width' => '100%', 'val' => "611" ],                 
          'events' => [ 'select2:select' =>'onSelectStreet' ],
          'items' => $streets,
          'value' => $model->street,
        ]);          
        echo '</div>';
      }

      //---- Список домов
      if( array_key_exists('facility',$model->attributes ) ) {
        $fasilities = ArrayHelper::map( TicketInputForm::getFacilitiesList($model->street, true), 'id', 'text' );
        //$fasilities = [["text"=>'Все','id'=>0]]+$fasilities;
        echo '<div class="form-group col-xs-2"> Дом :';
        echo   Select2Widget::widget([
          'name' => 'facility',
          'id' => 'facility',
          'settings' => [ 'width' => '100%' ],                 
          //'events' => [ 'select2:select' =>'onSelectFacility'],
          'items' => $fasilities,
          'value' => $model->facility,
        ]);
        echo '</div>';
      }

      //--- Серийный номер
      if( array_key_exists('serial',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="mfSerial"> Серийный №:'.
        Html::textinput('serial', $model->serial,['class'=>'form-control']).'</div>';
      }

      //---- Тип счетчика
      if( array_key_exists('type',$model->attributes ) ) {
        $ctlist=MetersList::GetMeterTypesList();unset($ctlist['']);$ctlist=[""=>"Все"]+$ctlist;
        echo '<div class="form-group col-xs-2">'.' Тип :'.
        Html::dropDownList('type', $model->type,  $ctlist,['class'=>'form-control']).'</div>';;
      }

      //---- Дата показаний ОТ
      if( array_key_exists('datefrom',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspс :'.
        DatePicker::widget(['name'  => 'datefrom',
                                    'value'  => $model->datefrom,
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';
      }
      //---- Дата показаний ДО
      if( array_key_exists('dateto',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspпо :'.
        DatePicker::widget(['name'  => 'dateto',
                                    'value'  => $model->dateto,//date('d-M-y'),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';
      }
      //--- строка адреса
      if( array_key_exists('address',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-3" id="mfAddr"> Адрес:'.
        Html::textinput('address', $model->address,['class'=>'form-control']).'</div>';
      }

      //---- Тип счетчика
      if( array_key_exists('datapresent',$model->attributes ) ) {
        $list = [0=>"все", 1=>"показания есть", 2=>"показаний нет"];
        echo '<div class="form-group col-xs-2">'.' Наличие данных :'.
        Html::dropDownList('datapresent', $model->datapresent,  $list,['class'=>'form-control']).'</div>';;
      }

      echo '<div class="form-group col-xs-1">';
      echo Html::submitButton(Yii::t('app','Choose'),['class'=>'submit btn btn-success','id'=>'submitMeterFiltr']).'</div>';

      echo '</div>'; /* End of row*/
      echo Html::endForm();
      echo '</p>';
  echo '</div>';
?>

<SCRIPT>
function onSelectRegion()
{
    $.ajax({
         url: "<?php echo Url::toRoute(["/ticket-input/get-streets-list"]); ?>",
         type: "POST",
         dataType: "json",
         data: {District: $("#district").val(), f_all:true},
         success: function(datamas) {
                $("#street").html("");
                $("#street").select2({data:datamas, width:'100%'});
                onSelectStreet();
         },
         error:   function() {
                $("#street").html('AJAX error!');
         }

  });
  return false;
}

function onSelectStreet()
{
    $.ajax({
         url: "<?php echo Url::toRoute(["/ticket-input/get-facility-list"]); ?>",
         type: "POST",
         dataType: "json",
         data: {StreetId: $("#street").val(), f_all:true},
         success: function(datamas) {
                $("#facility").html("");
                $("#facility").select2({data:datamas, width:'100%'});
                //onSelectFacility();
         },
         error:   function() {
                $("#facility").html('AJAX error!');
         }

  });
  return false;
}
</SCRIPT>
