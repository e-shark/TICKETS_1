<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use frontend\models\Tickets;
/**
 *  Ticket view partial view
 */
//print_r($model->ticket);
?>
<div class="tickets-_viewtab">
    <?php 
        Yii::$app->formatter->defaultTimeZone ="Etc/GMT-2";
        //$isTicketRead = Tickets::isTicketBeenRead($model->ticket['id'],$model->ticket['tiexecutant_id']);
        if(strpos($model->ticket['tistatus'],'COMPLETE') )$isTicketRead = TRUE;
        $tiAttributes = [
            [                   
            'label' => 'Адрес',
            'value' => ($model->isUserDispatcher()?
                        ('<span style="font-weight:bold;color:#E9967A">'.$model->ticket['tiregion'].' р-н</span><br>'):'').
                        $model->ticket['tiaddress'],
            'format'=>'html'
            ],
            [                   
            'label' => 'Проблема',
            'value' => $model->ticket['tiproblemtypetext'].' ('.$model->ticket['tiproblemtext'].')<br>'.$model->ticket['tidescription'],
            'format'=>'html'
            ],
            [                   
            'label' => 'Статус',
            'format'=>'html',
            'value' => Yii::$app->formatter->asDate($model->ticket['tistatustime'],"dd-MM-yyyy HH:m:s").' : '.Yii::$app->params['TicketStatus'][$model->ticket['tistatus']].
                        (empty($model->ticket['tistatusremote']) ? '':('<br><span style="font-weight:normal;color:#E9967A">1562: '.$model->ticket['tistatusremote'])).'</span>',
            //'contentOptions'=>  ('MASTER_COMPLETE'== $model->ticket['tistatus'] ) ? ['style'=>'background-color:lightgreen']:[],
            'contentOptions'=>  (strpos($model->ticket['tistatus'],'COMPLETE') ) ? ['style'=>'background-color:lightgreen']:
                                (strpos($model->ticket['tistatus'],'REFUSE')  ? ['style'=>'background-color:yellow']:
                                (strpos($model->ticket['tistatus'],'REASSIGN')?['style'=>'background-color:red;color:white']:[])),
            ],
            [                   
            'label' => 'Приоритет',
            //'value' => ($model->ticket['tipriority']=='NORMAL')?'Обычный':'Высокий',
            'value' =>Yii::$app->params['TicketPriority'][$model->ticket['tipriority']],
            'contentOptions'=> ( $model->ticket['tipriority'] < 'NORMAL') ? ['style'=>'color:red']:[]
            ],
            [                   
            'label' => 'Плановый срок исполнителю ',
            'format'=>['date','dd-MM-yyyy  HH:m'],
            'value' => $model->ticket['tiiplannedtime'],
            'contentOptions'=> (( strtotime($model->ticket['tiiplannedtime']) < time() ) &&
                                ( !in_array( $model->ticket['tistatus'], ['MASTER_COMPLETE','DISPATCHER_COMPLETE','1562_COMPLETE'] ) ) ) ?
                                //('MASTER_COMPLETE'!= $model->ticket['tistatus'])) ? 
                                ['style'=>' color:red'] : []
            ],
            ];
            if( !$model->isUserFitter() ) $tiAttributes = array_merge( $tiAttributes, [
                [                   
                'label' => 'Открыл заявку',
                'format'=>'html',
                'value' => $model->ticket['tioriginator'].'<br> '.$model->ticket['originatordeskname'],
                ],
                [                   
                'label' => 'Источник',
                'value' => $model->ticket['ticalltype']
                ],
                [                   
                'label' => 'Заявитель',
                'format'=>'html',
                'value' => ($model->ticket['ticaller']?$model->ticket['ticaller']:'-').
                            ' (тел.'.($model->ticket['ticallerphone']?$model->ticket['ticallerphone']:'-').')<br>'.$model->ticket['ticalleraddress']
                ],
                [                   
                'label' => 'Дата открытия заявки ',
                'format'=>['date','dd-MM-yyyy  HH:m:s'],
                'value' => $model->ticket['tiopenedtime']
                ],
                [                   
                'label' => 'Плановый срок',
                'format'=>['date','dd-MM-yyyy  HH:m'],
                'value' => $model->isUserFitter() ? $model->ticket['tiiplannedtime']:$model->ticket['tiplannedtimenew'],
                'contentOptions'=>( ( strtotime($model->ticket['tiplannedtimenew']) < time() ) &&
                                ( !in_array( $model->ticket['tistatus'], ['MASTER_COMPLETE','DISPATCHER_COMPLETE','1562_COMPLETE'] ) ) ) ?
                                ['style'=>'color:red'] : []
                ],
                [                   
                'label' => 'Плановый срок поставки МТЦ',
                'value' => $model->ticket['tisplannedtime'],
                'contentOptions'=> (( strtotime($model->ticket['tisplannedtime']) < time() ) &&
                                    ( !in_array( $model->ticket['tistatus'], ['MASTER_COMPLETE','DISPATCHER_COMPLETE','1562_COMPLETE'] ) ) ) ?
                                    ['style'=>'color:red'] : []
                ],
                [                   
                'label' => 'Объект',
                'format'=>'html',
                'value' => $model->ticket['tiobject'].' № <span style="font-weight:bold"> '.$model->ticket['tiobjectcode'].'</div> 
                (Дом № <span style="font-weight:bold">'.$model->ticket['tifacilitycode'].'</span> ) <br> '.$model->ticket['divisionname'],
                //'contentOptions'=> ['style'=>' font-weight:bold']
                ],
                [                   
                'label' => 'Ответственное подразделение',
                'value' => $model->ticket['deskname']
                ],
                [ 
                'label' => 'Исполнитель',
                'format'=>'html',
                'value' => isset($model->ticket['executant']) ?
                    ($model->ticket['executantdeskname'].'<br>'.$model->ticket['executant'].
                        ($model->ticket['tiexecutantread'] ? ' <span class="glyphicon glyphicon-folder-open" style="color:green"></span> ':
                    ' <span class="glyphicon glyphicon-envelope" style="color:red"></span> ')):'-',
                //'value' => $model->ticket['executant'].($isTicketRead?'':' (не прочитано)'),
                'contentOptions'=> $model->ticket['tiexecutantread'] ? []:['style'=>' font-weight:bold']
                ],
                [                   
                'label' => 'Неисправность',
                'format'=>'html',
                'value'=>($model->ticket['oostypetext']?'<b>'.$model->ticket['oostypetext'].'</b>':'<b style="color:red">ПРИЧИНА НЕ ОПРЕДЕЛЕНА</b>').'<br>'.
                          $model->ticket['tiresulterrorcode'].": ".$model->ticket['tiresulterrortext']
                ],
            ]);
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $tiAttributes,
    ]) ?>
    <?php /*print_r ($model->flist)*/ ?>

    
    
    <?php /*All parameters passing to beginForm will be in get, to be in the post, hidden fields need to be defined */?>
    <?= Html::beginForm(['appoint','ticketId'=>$model->ticket['id']],'post') ?>

    <?= Html::hiddenInput('ticketId'    ,$model->ticket['id'])?>
    <?= Html::hiddenInput('senderId'    ,$model->useroprights['id'])?>
    <?= Html::hiddenInput('senderdeskId',$model->useroprights['division_id'])?>
    <?= Html::hiddenInput('servicedeskId',$model->ticket['tidivision_id'])?>
    <?= Html::hiddenInput('actor'       ,$model->actor )?>


    <?php // Comment
        if(($model->isUserMaster()    && (!in_array($model->ticket['tistatus'],['MASTER_COMPLETE','DISPATCHER_COMPLETE'] )))   ||
             ($model->isUserDispatcher() && ($model->ticket['tistatus']!='DISPATCHER_COMPLETE'))   ||
             ($model->isUserFitter()    && (!in_array($model->ticket['tistatus'],['EXECUTANT_COMPLETE','MASTER_COMPLETE','DISPATCHER_COMPLETE'] )))){ ?>
            
    <h4 align='middle' class="glyphicon     glyphicon glyphicon-pencil" style='color:RoyalBlue'></h4>
    <?= Html::label('Комментарий :') ?>
    <?= Html::input('text', 'tiltext','',['class'=>'form-control','size'=>50])?>
    <?php }?>
    
    <?php 
    
    //----User is MASTER or DISPATCHER:
    if( ( $model->isUserMaster()      && (!in_array($model->ticket['tistatus'],[/*'MASTER_COMPLETE',*/'DISPATCHER_COMPLETE'] ) ) ) || 
        ( $model->isUserDispatcher()) && ($model->ticket['tistatus']!='DISPATCHER_COMPLETE') ) {

        //---PANEL: OOS TYPE panel
        $tihours = intval((time()-strtotime($model->ticket['tiopenedtime']))/3600);//echo $tihours;
        $oostypepanelclass  = $model->ticket['tioostype_id'] ? "panel panel-info":"panel panel-danger";
        echo "<div class='$oostypepanelclass' ><div class='panel-heading'>".
            Html::label('Причина неисправности лифта :').' ';
            if(!$model->ticket['tioostype_id'])echo Html::label('На указание причины осталось часов: '.($tihours<24?24-$tihours:0));
            echo '<div class="row">'.
                '<div class="col-md-3">'.
                    Html::dropDownList('tioostypeId', $model->ticket['tioostype_id'],  $model->getOosTypesList(),['class'=>'form-control']).
                '</div>';
                 if(!$model->ticket['tioostype_id'])echo '<div class="col-md-6">'.
                    "<b>ВНИМАНИЕ!</b> Необходимо указать причину неисправности в течение не более 24&nbspчасов c момента открытия заявки! Истекло часов : <b>$tihours</b>".
                '</div>';
                 echo '<div class="col-md-2">'.
                    Html::submitButton(Yii::t('app','Save'),['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_ASSIGN_OOS']) ]).
                '</div>'.
        '</div></div></div>';
        //---PANEL: OOS TIMES panel
        $oospnlclass  = $model->hasOos ? "panel panel-danger" : "panel panel-success";
        $oosdatespanelstyle = $model->hasOos ? "" : "display:none;";

        echo "<div class='$oospnlclass' ><div class='panel-heading'>";
        echo Html::label('Информация об Аварийной Остановке Лифта : ')." ".
             Html::label($model->hasOos ?($model->oosHours." час. простоя. ".($model->hasOosNow?"ОСТАНОВЛЕН":"РАБОТАЕТ")):"лифт РАБОТАЕТ",null,['style'=>'font-size:20px;','align'=>'center']);
        echo '<div class="row">';
            //--- #oosdatespanel -  panel's visibility is controlled by PHP/JS regarding on the ticket's OOS state
            echo "<div id='oosdatespanel' style='$oosdatespanelstyle'>";
                //---Choose the OOS begin time
                echo '<div class="col-md-2">';
                    echo 'Дата остановки';
                    echo DatePicker::widget(['name'  => 'tioosbegin',
                                        'value'  => $model->ticket['tioosbegin'],
                                        'dateFormat' => 'dd-MM-yyyy',
                                        'options'=>['class'=>'form-control']]);
                echo '</div>';
                echo '<div class="col-md-1"><br>';
                    echo Html::dropDownList('tioosbegintm',  Yii::$app->formatter->asDate($model->ticket['tioosbegin'],"HH"), $model->getHoursList(),['class'=>'form-control-sm','style'=>'width:100%;']);
                echo '</div>';
                //---Choose the OOS end time
                echo '<div class="col-md-2">';
                    echo 'Дата запуска';
                    echo DatePicker::widget(['name'  => 'tioosend',
                                        'value'  => $model->ticket['tioosend'],
                                        'dateFormat' => 'dd-MM-yyyy',
                                        'options'=>['class'=>'form-control']]);
                echo '</div>';
                echo '<div class="col-md-1"><br>';
                    echo Html::dropDownList('tioosendtm',  Yii::$app->formatter->asDate($model->ticket['tioosend'],"HH"), $model->getHoursList(),['class'=>'form-control-sm']);
                echo '</div>';
            echo '<div class="col-md-2"><br>'.
                Html::submitButton(Yii::t('app','Save'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_EDIT_OOS']) ]).' '.
            '</div>';
            echo '</div>';  // end of #oosdatespanel
            //---OOS IN/OUT button
            $oosbtntext=$model->hasOos ?"Отменить останов":"Остановить лифт";
            $oosbtnclass=$model->hasOos ?"submit btn btn-primary":"submit btn btn-danger";
            echo '<div class="col-md-2"><br>'.
                Html::submitButton(Yii::t('app',$oosbtntext),['class'=>"$oosbtnclass",'formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_SWITCH_OOS']) ]).
            '</div>';
        echo '</div></div></div>';

        //---PANEL: Set ticket plannet time only if user is 
        if( $model->isUserMaster() || $model->isUserDispatcher() ) {
            echo
            '<div class="panel panel-info"><div class="panel-heading">';
                echo Html::label('Плановый срок по заявке:');
                echo '<div class="row"><div class="col-md-6">';
                echo DatePicker::widget(['name'  => 'ticketplanneddate','value'  => $model->ticket['tiplannedtimenew'],'dateFormat' => 'yyyy-MM-dd','options'=>['class'=>'form-control']]);
                echo '</div><div class="col-md-3">';
                echo Html::submitButton(Yii::t('app','Set'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_ASSIGN_DATE']) ]);
            echo
            '</div></div></div></div>';
        }

        //---EXECUTANT PANEL
        echo '<div class="panel panel-success"><div class="panel-heading">';
        echo Html::label('Исполнитель, плановый срок исполнителю :');
        echo '<div class="row">';

            //---Choose the executant
            echo '<div class="col-md-4">';
                echo Html::dropDownList('receiverId', $model->ticket['tiexecutant_id'],  $model->fitterslist,['class'=>'form-control']);
            echo '</div>';

            //---Set planned time for executant
            echo
            '<div class="col-md-2">'.
                DatePicker::widget(['name'  => 'fitterplanneddate',
                                    'value'  => $model->ticket['tiiplannedtime'] ? $model->ticket['tiiplannedtime']:$model->ticket['tiplannedtimenew'],
                                    'dateFormat' => 'yyyy-MM-dd',
                                    'options'=>['class'=>'form-control']]).
            '</div>';

            //---Assign/Reassign/Close buttons
            echo '<div class="col-md-4">';
                if( 'EXECUTANT_COMPLETE' == $model->ticket['tistatus'] ) { 
                
               if((!$model->hasOosNow) AND !empty($model->ticket['tioostype_id'])) echo 
                Html::submitButton(Yii::t('app','Accept job'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_COMPLETE']) ]).' ';
                echo 
                Html::submitButton(Yii::t('app','Reject job'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_REASSIGN']) ]);
                }  else {
                echo
                Html::submitButton(Yii::t('app','Appoint'), ['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_ASSIGN']) ]);
                }
            echo '</div>';
        echo
        '</div></div></div>';

        //---TRANSFER to MASTER PANEL
        if( $model->isUserDispatcher() ) if($model->ticket['tistatus']!='DISPATCHER_COMPLETE')
                if( ( $model->ticket['tidivision_id'] === null ) || ($model->ticket['tidivision_id'] != $model->ticket['tidesk_id'])) {
            echo '<div class="panel panel-info"><div class="panel-heading">';
            echo Html::label('Ответственное подразделение:');
            echo '<div class="row"><div class="col-md-6">';
            echo Html::dropDownList('deskId', $model->ticket['tidivision_id'],  $model->getMasterDesksList(),['class'=>'form-control']);
            echo '</div><div class="col-md-3">';
            echo ' '.Html::submitButton(Yii::t('app','Transfer to Master'),['class'=>'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_ASSIGN_MASTER']) ]).'<br><br>';
            echo '</div></div></div></div>';
        }

} // end of Master/Dispatcher fields

        //------------Accept buttons
        if(($model->isUserMaster()&&in_array($model->ticket['tistatus'],['MASTER_REFUSE','DISPATCHER_REFUSE','DISPATCHER_ASSIGN','DISPATCHER_ASSIGN_MASTER','DISPATCHER_REASSIGN','OPERATOR_ASSIGN'])) ||
            ($model->isUserDispatcher()&&in_array($model->ticket['tistatus'],['MASTER_REFUSE','DISPATCHER_REFUSE','OPERATOR_ASSIGN','1562_ASSIGN','DISPATCHER_COMPLETE']))) echo
         Html::submitButton(Yii::t('app','Accept'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_ACCEPT']) ]);

        //------------Refuse buttons
        if( $model->isUserMaster() && (!in_array($model->ticket['tistatus'],['MASTER_COMPLETE','DISPATCHER_COMPLETE']))) echo 
         ' '.Html::submitButton(Yii::t('app','Refuse'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_REFUSE']) ]);

        //------------Close button
        if((!$model->hasOosNow) AND !empty($model->ticket['tioostype_id'])) {
            if( ( $model->isUserMaster()      && (!in_array($model->ticket['tistatus'],['MASTER_COMPLETE','DISPATCHER_COMPLETE'] ) ) ) || 
                ( $model->isUserDispatcher()) && ($model->ticket['tistatus']!='DISPATCHER_COMPLETE') ) echo ' '.
                Html::submitButton(Yii::t('app','Close Ticket'),['class'=>'submit btn btn-success','formaction'=>Url::toRoute(['appoint','tistatus'=>$model->actor.'_COMPLETE']) ]);
        } else if(!$model->isUserFitter()){ //---Alarming panels
            echo '<div class="panel panel-danger"><div class="panel-heading">';
            if($model->hasOosNow)echo '<div><b>ВНИМАНИЕ!</b> Лифт остановлен! Для закрытия заявки введите дату и время запуска лифта в панели Инфомации об Аварийной Остановке Лифта!</div>';
            if(empty($model->ticket['tioostype_id'])) echo '<div><b>ВНИМАНИЕ!</b> Причина неисправности лифта не определена! Для закрытия заявки укажите причину неисправности в панели выбора Причины Неисправности Лифта!</div>';
            echo '</div></div>';
        }

        //------------Redirect to 1562 button
        if( $model->isUserDispatcher() ) if( FALSE !== mb_strpos( $model->ticket['ticalltype'], '1562' ) ) if( !empty($model->ticket['ticoderemote'] ) ) {
            $url2062="http://062.mvk/LIFT/card_pere.php?".http_build_query(['c'=>$model->ticket['ticoderemote'],'m'=>6 ]);
            echo " <a href=\"$url2062\" target='_blank' hreflang='en' charset='windows-1251' class='submit btn btn-default'>Перейти в систему 1562</a>";
        }

        //----User is FITTER:
        if( $model->isUserFitter() ) if( FALSE === mb_strpos($model->ticket['tistatus'],'COMPLETE') ){ 
            //print_r($model->elerrorcodelist);
            echo Html::dropDownList('errorcode', 0,  ArrayHelper::map($model->elerrorcodelist,'errorcode','errortext'),['class'=>'form-control']);
            echo Html::submitButton(Yii::t('app','Done'), ['class' => 'submit btn btn-primary','formaction'=>Url::toRoute(['appoint','tistatus'=>'EXECUTANT_COMPLETE']) ]).' ';
            echo Html::submitButton(Yii::t('app','Refuse'),['class'=>'submit btn btn-danger','formaction'=>Url::toRoute(['appoint','tistatus'=>'EXECUTANT_REFUSE']) ]);
        }
    ?>
    <?= Html::endForm() ?>
    
  
</div>
