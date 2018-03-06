<?php

/* @var $this yii\web\View */
/*
 * It's an example code for 2 methods of passing parameters to view (se TicketsController.php):
 *	1. Push method:	using push we're getting $tilist1 and $provider here
 *	2. Pull method:	using pull we're getting $here tilist2
 */
//use Yii;
use yii\helpers\Url;
use yii\grid\GridView;

?>
    <?php 
		$tiColumns = [
			[
				'label'=>'Пр.', 
				'attribute' => 'tipriority',
				'content' => function($data){
					switch($data['tipriority']){
						case 'NORMAL': return '-';
						case 'EMERGENCY':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red"></span>';
						case 'CONTROL1':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red">1</span>';
						case 'CONTROL2':return '<span class="glyphicon glyphicon-exclamation-sign" style="color:red">2</span>';
					}
				},
				 //'value' => "ggg",//($model->ticket['tipriority']=='NORMAL')?'N':'H',
				// 'contentOptions' => ['class'=> ($data['tipriority']=='NORMAL')?"glyphicon glyphicon-eye-open":""]
			],
			[	
				'label'=>'Номер',
				'attribute' => 'ticode', 
				'content' => function($data){ 
					$cremote = $data['ticoderemote'];
					$url = Url::toRoute(['tickets/view', 'id' => $data['id']]);
					return  "<a href=$url>".$data['ticode'].'</a>'.($cremote?" <span class='glyphicon glyphicon-link' style='color:#E9967A;vertical-align:super;font-size:80%'></span><br><span style='font-weight:normal;font-size:11px;color:#E9967A'>$cremote</span>":'') ;},
				'format'		=>'html'
		    ],
		];
		if(!$model->isUserFitter())array_push($tiColumns, 
			[
				'label'=>'Дата создания',
				'attribute' => 'tiopenedtime',	
				'format'=>['date','dd-MM-yyyy']
			]
		);
		array_push( $tiColumns, 
			[
				'label'=>'Срок устранения',
				'attribute' => $model->isUserFitter()?'tiiplannedtime':'tiplannedtimenew',
				'format'=>['date','dd-MM-yyyy'],
				'contentOptions'=> function($data){ return ( !strpos($data['tistatus'],'COMPLETE') && strtotime($data['tiplannedtimenew']) < time() ) ? ['style'=>'color:red']:[];},
			]
		);
				//['attribute' => 'tistatustime','label'=>'Дата статуса'],
		array_push($tiColumns,
			[
				'label'=>'Адрес', 
				'attribute' => 'tiaddress'
			]
		);
		if( !$model->isUserFitter() ) $tiColumns = array_merge( $tiColumns, [
			[
				'label'=>'Статус',
				'format'=>'html',
				'attribute' => 'tistatus',		
				'content' => function($data){ 
					if(!empty($data['tioosbegin'])){	 // Is the elevator now or had been before in the state of OOS ?
						$inOos=empty($data['tioosend']); // Is the elevator in OOS now?
						$ooshours = intval( ((empty($data['tioosend'])?time():strtotime($data['tioosend'])) - strtotime($data['tioosbegin']))/3600 );
						if((!$inOos AND ($ooshours<24)))unset($ooshours); // Do not show OOS infos if OOS-time < 24h
					}
					return Yii::$app->params['TicketStatus'][ $data['tistatus' ]].	// Status
					(empty($data['tistatusremote']) ? '' :							// Remote Status (1562) 
						('<br><span style="font-weight:normal;font-size:11px;color:#E9967A">1562: '.$data['tistatusremote'].'</span>')).
					((!$ooshours) ? '' :												// OOS infos
						('<br><span style="font-weight:bold;color:#9F0000">Часов в простое: '.$ooshours.'. '.($inOos?'ОСТАНОВЛЕН':'РАБОТАЕТ').'</span>'));
				},
				'contentOptions'=> function($data){return 
					('MASTER_COMPLETE'== $data['tistatus'] ) ? ['style'=>'background-color:lightgreen']:
					(strpos($data['tistatus'],'REFUSE' ) ? ['style'=>'background-color:yellow']:
					(in_array($data['tistatus'],['DISPATCHER_COMPLETE','OPERATOR_COMPLETE','1562_COMPLETE','KAO_COMPLETE']) ? ['style'=>'background-color:lightgreen']:
					(strpos($data['tistatus'],'REASSIGN' ) ? ['style'=>'background-color:red;color:white']:[])));
				}
				//'value'=>//$statustxt[$data['tistatus']]
			],
			[
				'attribute' => 'executant',	
				'label'=>'Исполнитель',
				//'content'=>function($data){return $data['executant'];} 
				//'content'=>function($data){return $data['executant'].($data['tiexecutantread']=='1'?' <span class="glyphicon glyphicon-ok" style="color:green"></span> ':
	            //(isset($data['tiexecutant_id'])?' <span class="glyphicon glyphicon-envelope" style="color:red"></span> ':'-'));}
	            'content'=>function($data){return (!isset( $data['tiexecutant_id'] ))?'-':
	            	($data['executant'].($data['tiexecutantread']=='1'?' <span class="glyphicon glyphicon-folder-open" style="color:green"></span> ':' <span class="glyphicon glyphicon-envelope" style="color:red"></span> '));}
            ],
					
		]);
		array_push( $tiColumns, ['class' => 'yii\grid\ActionColumn', 'template' => '{view}','controller'=>'tickets']);
		
    	echo GridView::widget([
			'dataProvider' => $provider,
			'columns' => $tiColumns
		]);
	?>

    <?php/*<code><?= __FILE__ ?></code>*/?>
