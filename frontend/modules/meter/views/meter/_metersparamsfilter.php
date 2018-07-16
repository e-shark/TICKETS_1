<?php  
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use frontend\modules\meter\models\MetersList;

  //--- Filtering panel1: District,Calltype,Datefrom,Dateto
  echo "<div id='meterparamsfilter'>";
      echo '<p>';

      //----Filter all button
      echo Html::beginForm([$this->context->getRoute()],'get',['class'=>'form','id'=>'MeterFiltr']);
      echo '<div class="row">';

      //---Additional query string
      if( array_key_exists('serial',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="mfSerial"> Серийный №:'.
        Html::textinput('serial', $model->serial,['class'=>'form-control']).'</div>';
      }

      //----Meter types list
      if( array_key_exists('type',$model->attributes ) ) {
        $ctlist=MetersList::GetMeterTypesList();unset($ctlist['']);$ctlist=[""=>"Все"]+$ctlist;
        echo '<div class="form-group col-xs-2">'.' Тип :'.
        Html::dropDownList('type', $model->type,  $ctlist,['class'=>'form-control']).'</div>';;
      }

      //----Date from
      if( array_key_exists('datefrom',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspс :'.
        DatePicker::widget(['name'  => 'datefrom',
                                    'value'  => $model->datefrom,
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';
      }
      //----Date up to
      if( array_key_exists('dateto',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspпо :'.
        DatePicker::widget(['name'  => 'dateto',
                                    'value'  => $model->dateto,//date('d-M-y'),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';
      }
      //---Additional query string
      if( array_key_exists('address',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-3" id="mfAddr"> Адрес:'.
        Html::textinput('address', $model->address,['class'=>'form-control']).'</div>';
      }



      echo '<div class="form-group col-xs-1">';
      echo Html::submitButton(Yii::t('app','Choose'),['class'=>'submit btn btn-success','id'=>'submitMeterFiltr']).'</div>';

      echo '</div>'; /* End of row*/
      echo Html::endForm();
      echo '</p>';
  echo '</div>';
?>
