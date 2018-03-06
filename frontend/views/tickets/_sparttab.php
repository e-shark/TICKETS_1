<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use conquer\select2\Select2Widget;
use frontend\models\Tickets;

/**
 *	Ticket spare part partial view
 */

?>

<div class="tickets-_sparttab">
  
<?php if($model->isUserMaster() ) {
		ArrayHelper::multisort($model->PartsClassList,'elspcode');
		    echo '<div class="row">';
        echo '  <div class="col-md-8">';
		    echo    Html::dropDownList('cllist', 'null', ArrayHelper::map($model->PartsClassList,'elspcode','elspname'),['id'=>'ClassListSelect','class'=>'form-control','onChange'=>'onSelectClass()']);
        echo '  </div>';
        echo '</div>';
    }
?>

<?php Pjax::begin(['id' => 'spart-pjax', 'enablePushState' => false]); ?>

<?php if($model->isUserMaster() ) {
    echo Html::beginForm(['spartadd','id'=>$model->ticket['id']],'post',['data-pjax'=>"#spart-pjax"]);

		echo '<div class="row">';

    echo Html::hiddenInput('senderId'    ,$model->useroprights['id']);
    echo Html::hiddenInput('senderdeskId',$model->useroprights['division_id']);
    echo Html::hiddenInput('tistatus','MASTER_ASSIGN');

		    echo '  <div id="PartListdiv" class="col-md-8">';
          echo   Select2Widget::widget(
                [
                  'id' => 'PartListSelect',
                  'name' => 'spId',
                  //'items'=>ArrayHelper::map(Tickets::GetPartsList(0), 'id', 'text'),
                  //'data'=>Tickets::GetPartsList(0),
                  'settings' => [
                    'width' => '100%',
                  ],                 
                  'events' => [
                    'select2:select' =>'onSelectPart',
                  ],
                ]
            );        
        echo '</div>';	


        echo '  <div class="col-md-1">'.
                  Html::input('text','spNum','1',['id'=>'spNumInput','class'=>'form-control']).
             '  </div>';
        echo '  <div class="col-md-1">'.
                  Html::label('шт.','',['id'=>"PartUnitLabel"]).
             '  </div>';             
    echo '</div>';  //row

    echo Html::submitButton(Yii::t('app','Add'),['class'=>'submit btn btn-success','data-pjax'=>"#spart-pjax"]);

    echo Html::endForm();

}?>

  <div id='TicketsPartsDiv'>

  <?php 

    $columns = [
      ['attribute' => 'tiltime',    'label'=>'Дата'],
      /*[
        'attribute' => 'tilstatus',   
        'label'=>'Операция',
        'content' => function($data){ return Yii::$app->params['TicketStatus'][ $data['tilstatus']];},
        'contentOptions'=> function($data){return ('MASTER_COMPLETE'== $data['tilstatus'] ) ? ['style'=>'background-color:lightgreen']:[];}
      ],*/
      ['attribute' => 'tilspname',  'label'=>'Наименование'],
      ['attribute' => 'tilspquantity',  'label'=>'Кол.'],
      ['attribute' => 'tilspunit',  'label'=>'ед.'],
      //['attribute' => 'tiltext',    'label'=>'Комментарий'],
      ['attribute' => 'sender',   'label'=>'ФИО инициатора'],
      ['attribute' => 'receiver',   'label'=>'ФИО получателя'],
      ['attribute' => 'senderdesk',   'label'=>'Сервисное подразделение']
    ];

    if($model->isUserMaster() ) {     
      array_push($columns,
        ['class' => 'yii\grid\ActionColumn', 
          'template' => '{delete}',
          'urlCreator'=>function( $action, $model, $key, $index,  $this) use ($model) {return Url::toRoute(['spartdelete','id'=>$model->ticket['id'],'spartid'=>$key]); },
          'buttonOptions' => ['data-pjax'=>"#spart-pjax"],
        ]
      );
    }

    echo GridView::widget([ 'dataProvider' => $model->tispartprovider, 'columns' => $columns ]);

    Pjax::end();
  ?>


    </div>


	<?php
	//----User is MASTER:
    if($model->isUserMaster() ) { 
    	echo Html::beginForm(['spartaddsdate','id'=>$model->ticket['id']],'get');
	        echo Html::label('Плановый срок поставки :').
	        '<div class="row">'.
	            '<div class="col-md-2">'.
	                DatePicker::widget(['name'  => 'plannedsdate','value'  => $model->ticket['tisplannedtime'],'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]).
	            '</div>'.
	            '<div class="col-md-4">'.
	            	Html::submitButton(Yii::t('app','Set'),['class'=>'submit btn btn-success'/*,'formaction'=>Url::toRoute(['spartaddsdate','id'=>$model->ticket['id']])*/ ]).' '.
	                
	            '</div>'.
	        '</div>';
        echo Html::endForm();
     }
    ?>
</div>

<?php       
// Регистрация скриптов

$addr = Url::toRoute(["get-parts-list"]);
$addr2 = Url::toRoute(['spartadd','id'=>$model->ticket['id']]); 
$addr3 = Url::toRoute(['get-part-unit']); 
$vSenderId = $model->useroprights['id'];
$vSenderdeskId = $model->useroprights['division_id'];
$script = <<< JS

function onSelectPart(){
    $("#PartUnitLabel").html($("#PartListSelect").select2('data')[0].elspunit);
    return true;
};

function onSelectClass(){
    //$("#add-btn").hide();
    // $("#PartListdiv").html('-Загрузка данных-');
    $.ajax({
    	   url: '$addr',
         dataType: "json",
         data: {ClassStr: $("#ClassListSelect").val()},
         success: function(datamas) {
                $("#PartListSelect").html("");
                $("#PartListSelect").select2({data:datamas, width:'100%'});
                onSelectPart();
                $("#add-btn").show();
         },
         error:   function() {
              	$("#PartListdiv").html('AJAX error!');
         }

	});
	return false;
}

$(window).load(function () {
  $("#add-btn").hide();
  onSelectClass();
  $(document).on('pjax:complete', function() {
    onSelectClass();
    return true;
  })
});


JS;
$this->registerJs($script, yii\web\View::POS_END);
?>
