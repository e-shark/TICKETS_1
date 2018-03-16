<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;


class Report_Titotals extends Model
{
	public $datefrom;
	public $dateto;
	public $district;
	public $calltype;

    public function rules()
    {
        return [
			//[['dateto','datefrom'],'required'],
            [['dateto','datefrom'],'date','format'=>'php: d-m-Y'],
        ];
    }
       public function attributeLabels()
    {
        return [
            'district'=>'Район: ',
			'calltype'=>'Источник: ',
            'datefrom'=>'Дата от: ',
            'dateto'=>'Дата по: ',
        ];
    }

    public static function getStatusesList()
    {
    	$stlist = [0=>'Все',1=>'Выполненные',2=>'В работе',3=>'Отозванные'];
    	//--- Add all not HIDDEN values from params['TicketStatus']
    	foreach(Yii::$app->params['TicketStatus'] as $key=>$val) if( !strpos($key,'HIDDEN' ) ) $stlist += [$key=>$val];
    	return $stlist;    
    }
    public static function getStatusesListRemote()
    {
    	return [0=>'Все',1=>'Закр. КАО',2=>'Вып. исп-лем',3=>'Принята исп-лем',4=>'Нужно принять из 062',5=>'Откл'];    
    }
	/**
     * @param $model object with members: datefrom,dateto, district,calltype, [status], [tifindstr]
     * @param $params array with members: datefrom,dateto, district,calltype, [status], [tifindstr] 
     * @return string the sql predicate for filter
     */
    public static function fillparamsfiltet1( &$model, $params)
    {
		//---Preparу sql  statement for district
		if( array_key_exists('district',$model->attributes ) ) if( !empty($params['district'] ) ) {
			$model->district = $params['district'];
			$districtF=str_replace("'","\'",$model->district);
			$filtersql	.=" and (tiregion like '$districtF') ";
		}
		//---Load model and preparу sql  statement for repyear
		if( array_key_exists('repyear',$model->attributes ) ) {
			$model->repyear   = empty($params['repyear']) ?  date('Y') : $params['repyear'];
			$filtersql	.=" and (YEAR(tiopenedtime)=$model->repyear) ";
		}
		//---Load model and preparу sql  statement for remonth
		if( array_key_exists('repmonth',$model->attributes ) ) {
			$model->repmonth   = empty($params['repmonth']) ?  date('m') : $params['repmonth'];
			$filtersql	.=" and (MONTH(tiopenedtime)=$model->repmonth) ";
		}
		//---Preparу sql  statement for datefrom
		if( array_key_exists('datefrom',$model->attributes ) ) {
			$model->datefrom   = ( !empty($params['datefrom'] ) ) ? $params['datefrom'] :
				Yii::$app->db->createCommand("SELECT tiopenedtime FROM ticket ORDER BY tiopenedtime ASC LIMIT 1")->queryOne()['tiopenedtime'];
			//$model->datefrom = $params['datefrom'];
			try{$dateiso=Yii::$app->formatter->asDate($model->datefrom,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=null;}
			$model->datefrom = $dateiso;
			$filtersql	.=" and (tiopenedtime>'$model->datefrom') ";
		}
		//---Preparу sql  statement for dateto
		if( array_key_exists('dateto',$model->attributes ) ) {
			$model->dateto   = empty($params['dateto']) ?  date('d-M-y') : $params['dateto'];
			try{$dateiso=Yii::$app->formatter->asDate($model->dateto,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=date('d-M-y'); }
			$model->dateto = $dateiso;
			if($model->dateto<$model->datefrom)$model->dateto=$model->datefrom;
			$filtersql	.=" and (tiopenedtime<='$model->dateto  23:59:59') ";
		}
		//---Prepare sql  statement for additional query string [code,1562 code,lift code,address]
		if( array_key_exists('tifindstr',$model->attributes ) ) if( !empty($params['tifindstr'] ) ) {
            $model->tifindstr = $params['tifindstr'];
            $fstrar = explode(' ',$model->tifindstr);
            for($i=0;$i<count($fstrar);$i++)if($i+1<count($fstrar))$fstrar[$i]=$fstrar[$i].'%';
            $fstr=implode( $fstrar );
            //$filtersql = " and ((ticket.ticode like '%$model->tifindstr%' ) OR (ticket.ticoderemote like '%$model->tifindstr%' )  OR (ticket.tiobjectcode like '%$model->tifindstr%') OR (ticket.tiaddress like '%$model->tifindstr%')) ";
        	$filtersql .= " and ((ticode like '%$fstr%' ) OR (ticoderemote like '%$fstr%' )  OR (tiobjectcode like '%$fstr%') OR (tiaddress like '%$fstr%')) ";
		}
		//---Preparу sql  statement for calltype
		if( array_key_exists('calltype',$model->attributes ) ) if( !empty($params['calltype'] ) ) {
			$model->calltype = $params['calltype'];
			if($model->calltype ==1 )		$filtersql	.=" and (tidesk_id!=6 or tidesk_id is null) ";	// ЦДС 
			else if($model->calltype ==2 )	$filtersql	.=" and (tidesk_id=6) ";	// ОДС (без ЦДС)
			else 							$filtersql	.=" and (ticalltype like '$model->calltype') ";
		}
		//---Preparу sql  statement for tiobjectcode,
		if( array_key_exists('tiobjectcode',$model->attributes ) ) if( !empty($params['tiobjectcode'] ) ) {
			$model->tiobjectcode = $params['tiobjectcode'];
			$filtersql	.=" and (tiobjectcode='$model->tiobjectcode') ";
		}
		//---Preparу sql  statement for Executant division,
		if( array_key_exists('tiexecutantdesk',$model->attributes ) ) if( !empty($params['tiexecutantdesk'] ) ) {
			$model->tiexecutantdesk = $params['tiexecutantdesk'];
			$filtersql	.=" and (executantdeskid='$model->tiexecutantdesk') ";
		}
		//---Preparу sql  statement for Executant, assumes the name is Lastname [firstname] [patronymic]
		if( array_key_exists('tiexecutant',$model->attributes ) ) if( !empty($params['tiexecutant'] ) ) {
			$model->tiexecutant = $params['tiexecutant'];
			$fio = explode(' ',$model->tiexecutant,3); // Lastname [firstname] [patronymic]
			$sql = "SELECT id,division_id,lastname,firstname,patronymic from employee where (lastname like '$fio[0]%')";
			if($fio[1]) $sql .=" and (firstname like '$fio[1]%')";
			if($fio[2]) $sql .=" and (patronymic like '$fio[2]%')";
			//---Trying to find the Fitter
			$result = Yii::$app->db->createCommand($sql." and (oprights like '%F%')")->queryOne();
			$tiexecutant_id=$result['id'];
			if($tiexecutant_id) $filtersql	.=" and (tiexecutant_id= $tiexecutant_id) ";
			//---Otherwise trying to find the Master
			else {
				$result=Yii::$app->db->createCommand($sql." and oprights like '%M%'")->queryOne();
				$tiexecutant_id=$result['division_id'];
				if($tiexecutant_id) $filtersql	.=" and (tidesk_id= $tiexecutant_id) ";
				else $model->tiexecutant='';
			}
			if($tiexecutant_id)	$model->tiexecutant=$result['lastname'].' '.$result['firstname'].' '.$result['patronymic'];
		}

		//---Preparу sql  statement for status filter
		if(array_key_exists('status',$model->attributes )){
			$model->status = empty($params['status']) ?  '' : $params['status'];
			//if(!empty($model->status))$filtersql	 .=" and (tistatus like '$model->status') ";
			if(!empty($model->status))	switch($model->status){
				case 1:	// Выполненные
					$filtersql	 .=" and (tistatus in ('DISPATCHER_COMPLETE','1562_COMPLETE','KAO_COMPLETE','OPERATOR_COMPLETE')) ";
				break;
				case 2:	// В работе
					$filtersql	 .=" and (tistatus like 'MASTER_%' OR tistatus like 'EXECUTANT_%' OR 
					(tistatus in ('1562_ASSIGN','1562_REASSIGN','OPERATOR_ASSIGN','DISPATCHER_ACCEPT','DISPATCHER_ASSIGN','DISPATCHER_REASSIGN','DISPATCHER_ASSIGN_MASTER','DISPATCHER_ASSIGN_DATE')))";
				break;
				case 3:	// Отозванные
					$filtersql	 .=" and (tistatus in ('1562_REFUSE')) ";
				break;
				default:
					$filtersql	 .=" and (tistatus like '$model->status') "; // It's for automatically generated get, not supported in dropdownlist
				break;
			}
		}
		//---Preparу sql  statement for statusremote filter
		if(array_key_exists('statusremote',$model->attributes )){
			$model->statusremote = empty($params['statusremote']) ?  '' : $params['statusremote'];
			if(!empty($model->statusremote))	switch($model->statusremote){
				case 1:	// 
					$filtersql	 .=" and (tistatusremote like 'Закр. КАО') ";
				break;
				case 2:	// 
					$filtersql	 .=" and (tistatusremote like 'Вып. исп-лем') ";
				break;
				case 3:	// 
					$filtersql	 .=" and (tistatusremote like 'Принята исп-лем') ";
				break;
				case 4:	// 
					$filtersql	 .=" and (tistatusremote like 'Нужно принять из 062') ";
				break;
				case 5:	// 
					$filtersql	 .=" and (tistatusremote like 'Откл') ";
				break;
			}
		}
		//---Fill model->reportpagesize
		if(array_key_exists('reportpagesize',$model->attributes )){
			$model->reportpagesize = (!isset($params['reportpagesize'])) ?  20 : intval($params['reportpagesize']);
		}
		//if( FALSE === stristr($filtersql,"where" ) ) $filtersql = ' where '.ltrim($filtersql," and");
		return $filtersql;
    }
	public function generate($params)
	{	
		//$this->load($params);
		$fsql = self::fillparamsfiltet1($this,$params);
		//Yii::warning('READ==='.$this->dateto,__METHOD__);
		$sqltext = "select * from 
		(select  o.tiobject,o.tiobjectcode, 
				count(*) as total,
		        (select count(*) from ticket where tistatus in ('DISPATCHER_COMPLETE','1562_COMPLETE','KAO_COMPLETE','OPERATOR_COMPLETE') and (tiobject_id=o.id) $fsql) as completed,
		        (select count(*) from ticket where tistatus in ('1562_REFUSE') and (tiobject_id=o.id) $fsql) as revoked,
		        (select count(*) from ticket where tistatus in('1562_ASSIGN','OPERATOR_ASSIGN','DISPATCHER_ACCEPT','DISPATCHER_ASSIGN_DATE','DISPATCHER_ASSIGN_MASTER') and (tiobject_id=o.id) $fsql) as assigned,
		        (select count(*) from ticket where (tistatus like 'MASTER_%' or tistatus like 'EXECUTANT_%') and (tiobject_id=o.id) $fsql) as atwork
		        from (ticketobject o left join ticket t on o.id=t.tiobject_id) where (t.id>0) $fsql group by o.tiobject
		union all
		select  'ИТОГО','TOTAL',
				count(*)  as total,
		        (select count(*) from ticket where tistatus in ('DISPATCHER_COMPLETE','1562_COMPLETE','KAO_COMPLETE','OPERATOR_COMPLETE') $fsql) as completed,
		        (select count(*) from ticket where tistatus in ('1562_REFUSE') $fsql) as revoked,
		        (select count(*) from ticket where tistatus in('1562_ASSIGN','OPERATOR_ASSIGN','DISPATCHER_ACCEPT','DISPATCHER_ASSIGN_DATE','DISPATCHER_ASSIGN_MASTER') $fsql) as assigned,
		        (select count(*) from ticket where (tistatus like 'MASTER_%' or tistatus like 'EXECUTANT_%') $fsql) as atwork
		        from ticket where (ticket.id>0) $fsql) as tbl order by tiobjectcode";

		//$oprights = Tickets::getUserOpRights();
		//---Prepare the sql statement for tickets according to the user rights
		//if(FALSE !== $oprights )$sqltext = $sqltext.' and tidivision_id = '.$oprights[division_id];
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
		]);
		return $provider;
	}
}
	