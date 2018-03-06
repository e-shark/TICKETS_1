<?php  
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

  //--- Filtering panel1: District,Calltype,Datefrom,Dateto
  echo "<div id='paramsfilter1'>";
      echo '<p>';

      //----Filter all button
      echo Html::beginForm([/*'titotals'*/$this->context->getRoute()],'post',['id'=>'ajax_form','class'=>'form-inline']);

      //----Districts list
      if( array_key_exists('district',$model->attributes ) ) {
        echo ' Район :'.
        Html::dropDownList('district', $model->district,  Tickets::getDistrictsList(),['class'=>'form-control']).' ';
      }
      //----Report year
      if( array_key_exists('repyear',$model->attributes ) ) {
        echo ' Год :'.
        Html::dropDownList('repyear', $model->repyear,  Tickets::getYearsList(false),['class'=>'form-control']).' ';
      }
      //----Report month
      if( array_key_exists('repmonth',$model->attributes ) ) {
        echo ' Месяц :'.
        Html::dropDownList('repmonth', $model->repmonth,  Tickets::getMonthsList(false),['class'=>'form-control']).' ';
      }
      //----Date from
      if( array_key_exists('datefrom',$model->attributes ) ) {
        echo ' Дата&nbspс :'.
        DatePicker::widget(['name'  => 'datefrom',
                                    'value'  => $model->datefrom,
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]);
      }
      //----Date up to
      if( array_key_exists('dateto',$model->attributes ) ) {
        echo ' Дата&nbspпо :'.
        DatePicker::widget(['name'  => 'dateto',
                                    'value'  => $model->dateto,//date('d-M-y'),
                                    'dateFormat' => 'dd-MM-yyyy',
                                    'options'=>['class'=>'form-control']]);
      }
      //----Call types list
      if( array_key_exists('calltype',$model->attributes ) ) {
        echo ' Источник :'.
        Html::dropDownList('calltype', $model->calltype,  Tickets::getCallTypesList(),['class'=>'form-control']).' ';
      }
      //----Status
      if( array_key_exists('status',$model->attributes ) ) {
        echo ' Статус : '.
        //Html::dropDownList('status', $model->status,  [""=>'Все']+Yii::$app->params['TicketStatus'],['class'=>'form-control','style'=>'width:14%']).' ';
        Html::dropDownList('status', $model->status,  Report_Titotals::getStatusesList(),['class'=>'form-control'/*,'style'=>'width:14%'*/]).' ';
      }
      //---Additional query string
      if( array_key_exists('tifindstr',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo 'Заявка:'.
        Html::textinput('tifindstr', $model->tifindstr,['class'=>'form-control']).' ';
      }
      //---Executant
      if( array_key_exists('tiexecutant',$model->attributes ) ) {
        //echo '<div style="margin-top:5px">Заявка:'.
        echo 'Исполнитель:'.
        Html::textinput('tiexecutant', $model->tiexecutant,['class'=>'form-control']).' ';
      }

      echo Html::submitButton(Yii::t('app','Choose'),['id'=>'sbmbtn','class'=>'submit btn btn-success']);
      echo Html::endForm();
      echo '</p>';
  echo '</div>';
?>

<?php
$addr1 = Url::toRoute(["get-marker-list"]);
?>
<script type="text/javascript">
    function sendAjaxForm() {
    $.ajax({
      url:      '<?php echo $addr1?>' ,     //url страницы 
      type:     "POST",                     //метод отправки
      dataType: "html",                     //формат данных
      data: $("#ajax_form").serialize(),    // Сеарилизуем объект
      success: function(response) {         //Данные отправлены успешно
        result = $.parseJSON(response);
        <?php echo $fcallback?>(result)
      },
      error: function(response) {           // Данные не отправлены
      }
    });
  } 
</script>

<?php
$script = <<< JS

 $("#sbmbtn").click(
    function(){
      sendAjaxForm();
      return false; 
    }
  );  

  sendAjaxForm();
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

