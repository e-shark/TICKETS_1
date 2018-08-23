<?php
namespace frontend\models;
use yii;
use yii\base\Model;
//use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

class Report_RepairsList extends Model
{
	public $district;
	public $datefrom;
	public $dateto;
	//public $tifindstr;
	public $opstatus;

    public static function FillParamsFilterWODistrict( &$model, $params)
    {
    	$filtersql = "";
		//---Preparу sql statement for opstatus filter
		if(array_key_exists('opstatus',$model->attributes )){
			$model->opstatus = empty($params['opstatus']) ?  '' : $params['opstatus'];
			if(!empty($model->opstatus))	switch($model->opstatus){
				case 1:	// остановлен
					$filtersql	 .=" and (elopstatus = 0) ";
				break;
				case 2:	// не определено
					$filtersql	 .=" and (elopstatus is null) ";
				break;
				case 3:	// восстановлен
					$filtersql	 .=" and ((elopstatus = 1) and (tioosend is not null)) ";
				break;
				case 4:	// отремонтирован без останова
					$filtersql	 .=" and ((elopstatus = 1) and (tioosbegin is null)) ";
				break;
			}
		}
		//---Preparу sql  statement for datefrom
		if( array_key_exists('datefrom',$model->attributes ) ) {
			$model->datefrom   = ( !empty($params['datefrom'] ) ) ? $params['datefrom'] :
				Yii::$app->db->createCommand("SELECT tiopenedtime FROM ticket ORDER BY tiopenedtime ASC LIMIT 1")->queryOne()['tiopenedtime'];
			//$model->datefrom = $params['datefrom'];
			try{$dateiso=Yii::$app->formatter->asDate($model->datefrom,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=null;}
			$model->datefrom = $dateiso;
		}
		//---Preparу sql  statement for dateto
		if( array_key_exists('dateto',$model->attributes ) ) {
			$model->dateto   = empty($params['dateto']) ?  date('d-M-y') : $params['dateto'];
			try{$dateiso=Yii::$app->formatter->asDate($model->dateto,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=date('d-M-y'); }
			$model->dateto = $dateiso;
			if($model->dateto < $model->datefrom) $model->dateto = $model->datefrom;
		}
		$filtersql	.=" and (tioosbegin is not null) ";						// нас интересуют только лифты в останове
		if (!empty($model->dateto)) {
			$oosend  = $model->dateto;
			$filtersql	.= " and (tioosbegin < '$model->datefrom') ";		// не рассматриваем лифты, остановленные после интересующего периода
		}
		else $oosend = date('d-M-y');
		if (!empty($model->datefrom)) {
			$oosbegin  = $model->datefrom;
		}
		else $oosbegin = '2000-01-01';							// если не задано другое, то в качестве начала интервала берём "когда-то-давныим-давно"
		$filtersql	.=" and ((tioosend is null) or (tioosend > '$oosbegin')) ";	// нужны лифты не запущенные, или запущеные до конца интервала

		return $filtersql;
	}

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	public function FillParamsFilter(&$model, $params)
	{
		$filtersql = self::FillParamsFilterWODistrict($model, $params);

		//---Preparу sql  statement for district
		if( array_key_exists('district',$model->attributes ) ) if( !empty($params['district'] ) ) {
			$model->district = $params['district'];
			$districtF=str_replace("'","\'",$model->district);
			$filtersql	.=" and (tiregion like '$districtF') ";
		}
		return $filtersql;
	}

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	function CalcIntervalsSum($iedgs)
	{
Yii::warning("************************************************intervals***********************[\n".json_encode($iedgs)."\n]");
		ksort($iedgs);
Yii::warning("************************************************intervals sort***********************[\n".json_encode($iedgs)."\n]");

		// Складываем концы интервалов в интервалы
		$intervals = [];
		$count = 0;
		foreach($iedgs as $time=>$type){
			if ((0 == $count) && (1 == $type))
				$ni = ['begin' => $time];
			$count += $type;
Yii::warning("\n----- time ---------------------------------------\n cnt:".$count."  type:".$type."  time: (".$time.") ".date('d-m-Y H:i:s',$time)."\n");
			if ((0 == $count) && (-1 == $type)){
				$ni['end'] = $time;
				$intervals[] = $ni;
Yii::warning("\n----- add ---------------------------------------\n".date('d-m-Y H:i:s',$ni['begin'])." - ".date('d-m-Y H:i:s',$ni['end'])."\n");
			}
		}

		// Вычисляем суммарную протяженность интервалов
		$sum = 0;
		if (!empty($intervals)){
			foreach($intervals as $interval){
Yii::warning("\n----- suminterval ---------------------------------------\n".date('d-m-Y H:i:s',$interval['begin'])." - ".date('d-m-Y H:i:s',$interval['end'])."\n");
				try{ 
					$tb = $interval['begin'];
					$te = $interval['end'];
					$sum += $te - $tb;
				} 
				catch(\Exception $e){ continue; }
			}
		}
Yii::warning("\n----- SUM---------------------------------------\n sum=".$sum."    ".(int)($sum/60/60)."\n");
		return $sum;		
	}

	//--------------------------------------------------------------------------------------
	//	Расчитать интервалы простоя, сложив прости в каждой заявке по лифту
	//--------------------------------------------------------------------------------------
	public function MakeReportTable($params,$DateFrom, $DateTo, $OpStatus, $District)
	{
 		$f1sql = self::FillParamsFilterWODistrict($this,$params);

		$sqltext="SELECT ticket.id, ticket.tiaddress, ticket.tiobjectcode, tiequipment_id, ticode, tiregion, tiopenedtime, tioosbegin, tioosend, tiplannedtimenew,  oostypetext, tiproblemtypetext, tidescription, tiproblemtext, streetname, fabuildingno, elporchno, elporchpos, elinventoryno 
		from ticket left join (
			select e.*, os.tiopstatus as elopstatus, os.tistatustime as elstatustime  from elevator e
			left join (
				select ts.* from (select t.id, t.tiequipment_id, t.tiopstatus, t.tistatustime from ticket t order by t.tistatustime desc) ts group by ts.tiequipment_id
			) os on os.tiequipment_id = e.id			
		) el on ticket.tiobjectcode=el.elinventoryno
 		left join ticketproblemtype on ticket.tiproblemtype_id =ticketproblemtype.id 
 		left join oostype on ticket.tioostype_id=oostype.id
 		left join facility on ticket.tifacility_id =facility.id 
 		left join street on facility.fastreet_id =street.id
 		where tioosbegin is not null $f1sql order by tiregion, tiequipment_id ";

Yii::warning("\n---------------------------------SQL------------------\n".$sqltext.'\n');

		$tickets = Yii::$app->db->createCommand($sqltext)->queryAll();	
		$ReportTable = [];

		$intervals = [];
		$ReportTableRec['tiequipment_id'] = 0;
		$start = true;
		foreach($tickets as $ticket){
			$tbegin = $ticket['tioosbegin'];
			$tend = $ticket['tioosend'];
			if (empty($tbegin) and empty($tend)) continue;
			if (empty($tbegin)) $tbegin = $DateFrom;
			if ($tbegin < $DateFrom) $tbegin = $DateFrom;
			if (empty($tend)) $tend = $DateTo;
			if ($tend > $DateTo) $tend = $DateTo;
			try{ $ibegin = strtotime( $tbegin ); } catch(\Exception $e){ continue; }
			try{ $iend = strtotime( $tend ); } catch(\Exception $e){ continue; }
		Yii::warning("\n--- interval ---------------------------------------\n [".$ticket['tioosbegin']."]: ".$tbegin."  = ".$ibegin." \n [".$ticket['tioosend']."]: ".$tend."  = ".$iend."\n");
			while( isset($intervals[$ibegin]) ) $ibegin++;
			$intervals[$ibegin] = +1;
			while( isset($intervals[$iend]) ) $iend++;
			$intervals[$iend] =  -1;
		Yii::warning("\n--- int ext ---------------------------------------\n [".$ticket['tioosbegin']."]: ".date('d-m-Y H:i:s',$ibegin)."  = ".$ibegin." \n [".$ticket['tioosend']."]: ".date('d-m-Y H:i:s',$iend)."  = ".$iend."\n  eq_id:".$ticket['tiequipment_id']."\n  eq_cod:".$ticket['tiobjectcode']."\n");

			if ($ReportTableRec['tiequipment_id'] != $ticket['tiequipment_id']) {
				// следующий лифт
				if (!$start){
					// Делаем расчет и сохраняем запись по предидущему лифту
					Yii::warning("\n--- CALC ---------------------------------------\n eq_id:".$ticket['tiequipment_id']."\n  eq_cod:".$ticket['tiobjectcode']."\n");
					$sumtime = self::CalcIntervalsSum( $intervals );
					$ReportTableRec['oosumtime'] = (int)($sumtime/60/60);
					$ReportTable[] = $ReportTableRec;
				}
				// формируем новую запись
				$intervals = [];
				$ReportTableRec = [
					'tiequipment_id' => $ticket['tiequipment_id'],
					'tiobjectcode' => $ticket['tiobjectcode'],
					'tiaddress' => $ticket['tiaddress'],
					'tiregion' => $ticket['tiregion'],
					'oosumtime' => 0,
					//'' => $ticket[''],
				];
				$start = false;
			}

		}
		if (!$start){
			// Делаем расчет и сохраняем последнюю запись
			$sumtime = self::CalcIntervalsSum( $intervals );
			$ReportTableRec['oosumtime'] = $sumtime;
			$ReportTable[] = $ReportTableRec;
		}
		
		return $ReportTable;
	}

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
	public function generateReport($params)
	{
		//$intervals = self::MakeReportTable($params,'2000-01-01', date('d-M-y'), NULL, NULL);
		//$sum = self::CalcIntervalsSum($intervals);
//Yii::warning("\n----- sum ---------------------------------------\n".$sum."\n");
	}

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
	public function generateListOld($params)
	{
 		$f1sql = self::FillParamsFilter($this,$params);

		$sqltext="SELECT ticket.id, ticket.tiaddress, ticket.tiobjectcode,ticode, tiregion, tiopenedtime, tioosbegin, tioosend, tiplannedtimenew, TIMESTAMPDIFF(HOUR,
				IF ( (IFNULL(tioosbegin,'2000-01-01') < '{$this->datefrom}'), '{$this->datefrom}' , IFNULL(tioosbegin,'2000-01-01') ),
				IF ( (IFNULL(tioosend,now()) > '{$this->dateto}'), '{$this->dateto}' , IFNULL(tioosend,now()) )
			) as oostime, oostypetext, tiproblemtypetext, tidescription, tiproblemtext, streetname, fabuildingno, elporchno, elporchpos, elinventoryno 
		from ticket left join (
			select e.*, os.tiopstatus as elopstatus, os.tistatustime as elstatustime  from elevator e
			left join (
				select ts.* from (select t.id, t.tiequipment_id, t.tiopstatus, t.tistatustime from ticket t order by t.tistatustime desc) ts group by ts.tiequipment_id
			) os on os.tiequipment_id = e.id			
		) el on ticket.tiobjectcode=el.elinventoryno
 		left join ticketproblemtype on ticket.tiproblemtype_id =ticketproblemtype.id 
 		left join oostype on ticket.tioostype_id=oostype.id
 		left join facility on ticket.tifacility_id =facility.id 
 		left join street on facility.fastreet_id =street.id
 		where tioosbegin is not null $f1sql order by tiregion";

		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tiregion',
				],
				'defaultOrder' => [ 'tiregion' => SORT_ASC ],
			],
		]);
		return $provider;	
	}

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
	public function generateList($params)
	{
		$ReportTable = self::MakeReportTable($params,'2000-01-01', date('d-M-y'), NULL, NULL);
		$provider = new ArrayDataProvider([
			'allModels' => $ReportTable,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tiincidenttime',
					'ooshours',
				],
				'defaultOrder' => [ 'tiincidenttime' => SORT_ASC ],
			],
		]);
		return $provider;	
	}

}
