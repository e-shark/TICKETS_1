<?php  
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

/**
 * Filter panel for reports
 *
 * @var $model object should contain: district,datefrom,dateto,calltype
 *                may contain: status
 */


  //--- Filtering panel1: District,Calltype,Datefrom,Dateto
  echo "<div id='paramsfilter1'>";
      echo '<p>';
      /* Trying to use ActiveForm...
      $form = ActiveForm::begin(['id' => 'filtern-form','action'=>Url::toRoute(['titotals']),'method'=>'get','options'=>['class'=>'form-inline']]);
        echo $form->field($model,'district')->dropDownList(Tickets::getDistrictsList(),['class'=>'form-control']).' ';
        echo $form->field($model,'calltype')->dropDownList(Tickets::getCallTypesList(),['class'=>'form-control']).' ';
        echo $form->field($model,'datefrom')->widget(DatePicker::className(),['dateFormat'=>'dd-MM-yyyy','options'=>['class'=>'form-control']]).' ';
        echo $form->field( $model, 'dateto' )->widget(DatePicker::className(),['dateFormat' => 'dd-MM-yyyy','options'=>['class'=>'form-control']]).' ';
      echo  '<div class="form-group">';
      echo Html::submitButton(Yii::t('app','Set'),['class'=>'form-control submit btn btn-success']);
      echo  '</div>';
       ActiveForm::end();
      */
       
      //----Filter all button
      echo Html::beginForm([/*'titotals'*/$this->context->getRoute()],'get',['class'=>'form','id'=>'formFltr1']);
      echo '<div class="row">';

      //----Records per page
      if( array_key_exists('recperpage',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Записей на странице:'.
        Html::dropDownList('recperpage', $model->recperpage,  ['5'=>5, '10'=>10, '15'=>15, '20'=>20, '25'=>25, '30'=>30, '50'=>50, '100'=>100, '200'=>200],['class'=>'form-control']).'</div>';
      }
      
      //----Districts list
      if( array_key_exists('district',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Район :'.
        Html::dropDownList('district', $model->district,  Tickets::getDistrictsList(),['class'=>'form-control']).'</div>';
      }
      //----Report year
      if( array_key_exists('repyear',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Год :'.
        Html::dropDownList('repyear', $model->repyear,  Tickets::getYearsList(false),['class'=>'form-control']).'</div>';
      }
      //----Report month
      if( array_key_exists('repmonth',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Месяц :'.
        Html::dropDownList('repmonth', intval($model->repmonth),  Tickets::getMonthsList(false),['class'=>'form-control']).'</div>';
      }
      //----Date from
      if( array_key_exists('datefrom',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspс :'.
        DatePicker::widget(['name'  => 'datefrom',
                                    'value'  => $model->datefrom,
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';;
      }
      //----Date up to
      if( array_key_exists('dateto',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Дата&nbspпо :'.
        DatePicker::widget(['name'  => 'dateto',
                                    'value'  => $model->dateto,//date('d-M-y'),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]).'</div>';;
      }
      //----Call types list
      if( array_key_exists('calltype',$model->attributes ) ) {
        $ctlist=Tickets::getCallTypesList();unset($ctlist['']);$ctlist=[""=>"Все","1"=>'ЦДС',"2"=>'ОДС (без ЦДС)']+$ctlist;
        echo '<div class="form-group col-xs-2">'.' Источник :'.
        Html::dropDownList('calltype', $model->calltype,  $ctlist,['class'=>'form-control']).'</div>';;
      }
      //----Status
      if( array_key_exists('status',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Статус : '.
        //Html::dropDownList('status', $model->status,  [""=>'Все']+Yii::$app->params['TicketStatus'],['class'=>'form-control','style'=>'width:14%']).' ';
        Html::dropDownList('status', $model->status,  Report_Titotals::getStatusesList(),['class'=>'form-control'/*,'style'=>'width:14%'*/]).'</div>';
      }
      //----StatusRemote
      if( array_key_exists('statusremote',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2"> Статус 1562: '.
        Html::dropDownList('statusremote', $model->statusremote,  Report_Titotals::getStatusesListRemote(),['class'=>'form-control']).'</div>';
      }
      //---Additional query string
      if( array_key_exists('tifindstr',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="divtifindstr"> Заявка:'.
        Html::textinput('tifindstr', $model->tifindstr,['class'=>'form-control']).'</div>';
      }
      //---Objectcode
      if( array_key_exists('tiobjectcode',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="divtiobjectcode"> Инв.номер:'.
        Html::textinput('tiobjectcode', $model->tiobjectcode,['class'=>'form-control']).'</div>';
      }
      //---Executant division
      if( array_key_exists('tiexecutantdesk',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="divtivexecutantdesk"> Подр.исполнителя:'.
        Html::dropDownList('tiexecutantdesk', $model->tiexecutantdesk,  Tickets::getMasterDesksList(TRUE),['class'=>'form-control']).'</div>';
      }
      //---Executant
      if( array_key_exists('tiexecutant',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo '<div class="form-group col-xs-2" id="divtivexecutant"> Исполнитель:'.
        Html::textinput('tiexecutant', $model->tiexecutant,['class'=>'form-control']).'</div>';
      }
      //---opstatus 
      if( array_key_exists('opstatus',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2" id="opstatus"> Статус:'.
        Html::dropDownList('opstatus',$model->opstatus,[0=>'Все',1=>'Остановлен',2=>'Не определен',3=>'Восстановлен'/*,4=>'без останова'*/],['class'=>'form-control']).'</div>';
      }
      //---Report page size
      if( array_key_exists('reportpagesize',$model->attributes ) ) {
        echo '<div class="form-group col-xs-2" id="reportpagesize"> Строк:'.
        Html::dropDownList('reportpagesize',$model->reportpagesize,[20=>'20',25=>'25',50=>'50',100=>'100',200=>'200',500=>'500',1000=>'1000',0=>'Все'],['class'=>'form-control']).'</div>';
      }
      echo '<div class="form-group col-xs-1"><br>';
      echo Html::submitButton(Yii::t('app','Choose'),['class'=>'submit btn btn-success','id'=>'submitFltr1']).'</div>';
      echo '</div>'; /* End of row*/
      echo Html::endForm();
      echo '</p>';
  echo '</div>';
?>
