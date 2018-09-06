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
	public $dateend;
	//public $dateto;
	//public $tifindstr;
	public $opstatus;
	public $reportpagesize;

	public $ElParams;

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
    public function FillParams($params)
    {
		//---Preparу sql statement for opstatus filter
		if(array_key_exists('opstatus', $this->attributes )){
			$this->opstatus = is_null($params['opstatus']) ?  1 : $params['opstatus'];
		}

		//---Preparу sql  statement for datefrom
		if( array_key_exists('datefrom', $this->attributes ) ) {
			$this->datefrom   = ( !empty($params['datefrom'] ) ) ? $params['datefrom'] :
				Yii::$app->db->createCommand("SELECT tiopenedtime FROM ticket ORDER BY tiopenedtime ASC LIMIT 1")->queryOne()['tiopenedtime'];
			//$this->datefrom = $params['datefrom'];
			try{ $dateiso = Yii::$app->formatter->asDate($this->datefrom,'yyyy-MM-dd'); }catch(\Exception $e){ $dateiso=null;}
			$this->datefrom = $dateiso;
		}

		//---Preparу sql  statement for dateto
		if( array_key_exists('dateto', $this->attributes ) ) {
			$this->dateto   = empty($params['dateto']) ?  date('d-M-y') : $params['dateto'];
			try{ $dateiso = Yii::$app->formatter->asDate($this->dateto,'yyyy-MM-dd'); }catch(\Exception $e){ $dateiso=date('d-M-y'); }
			$this->dateto = $dateiso;
			if($this->dateto < $this->datefrom) $this->dateto = $this->datefrom;
		}
		$this->dateend = date('Y-m-d H:i:s');

		//---Preparу sql  statement for district
		if( array_key_exists('district', $this->attributes ) ) if( !empty($params['district'] ) ) {
			$this->district = $params['district'];
		}

		//---Preparу sql  statement for district
		if( array_key_exists('reportpagesize', $this->attributes ) ) {
			if( !empty($params['reportpagesize'] ) ) 
				$this->reportpagesize = $params['reportpagesize'];
			else
				$this->reportpagesize = 20;
		}
	}

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	function FillFilterDate()
	{
		$filtersql = "";

		$filtersql	.=" and (tioosbegin is not null) ";						// нас интересуют только лифты в останове
		if (!empty($this->dateend)) {
			$oosend  = $this->dateend;
			$filtersql	.= " and (tioosbegin < '$this->dateend') ";		// не рассматриваем лифты, остановленные после интересующего периода
		}
		else $oosend = date('d-M-y');
		if (!empty($this->datefrom)) {
			$oosbegin  = $this->datefrom;
		}
		else $oosbegin = '2000-01-01';							// если не задано другое, то в качестве начала интервала берём "когда-то-давныим-давно"
		$filtersql	.=" and ((tioosend is null) or (tioosend > '$oosbegin')) ";	// нужны лифты не запущенные, или запущеные до конца интервала

		return $filtersql;
	}

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	function FillFilter()
	{
		$filtersql = "";

		if(!empty($this->opstatus))	
			switch($this->opstatus){
				case 1:	// остановлен
					$filtersql	 .=" and (st = 0) ";
				break;
				case 2:	// не определено
					$filtersql	 .=" and (st is null) ";
				break;
				case 3:	// В работе
					$filtersql	 .=" and (st = 1)";
				break;
				case 4:	// отремонтирован без останова
					$filtersql	 .=" and ((st = 1) and (tioosbegin is null)) ";
				break;
			}

		if (!empty($this->district)){
		$districtF = str_replace("'","\'", $this->district);
		$filtersql	.=" and (tiregion like '$districtF') ";
		}

		$filtersql .= $this->FillFilterDate();

		return $filtersql;
	}

	//--------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------
	function CalcIntervalsSum($iedgs)
	{
		ksort($iedgs);
//Yii::warning("-------------------- intervals sort ------------------------[\n".json_encode($iedgs)."\n]");

		// Складываем концы интервалов в интервалы
		$intervals = [];
		$count = 0;
		foreach($iedgs as $time=>$type){
			if ((0 == $count) && (1 == $type))
				$ni = ['begin' => $time];
			$count += $type;
//Yii::warning("\n----- time ---------------------------------------\n cnt:".$count."  type:".$type."  time: (".$time.") ".date('d-m-Y H:i:s',$time)."\n");
			if ((0 == $count) && (-1 == $type)){
				$ni['end'] = $time;
				$intervals[] = $ni;
//Yii::warning("\n----- add ---------------------------------------\n".date('d-m-Y H:i:s',$ni['begin'])." - ".date('d-m-Y H:i:s',$ni['end'])."\n");
			}
		}

		// Вычисляем суммарную протяженность интервалов
		$sum = 0;
		if (!empty($intervals)){
			foreach($intervals as $interval){
//Yii::warning("\n----- suminterval ---------------------------------------\n".date('d-m-Y H:i:s',$interval['begin'])." - ".date('d-m-Y H:i:s',$interval['end'])."\n");
				try{ 
					$tb = $interval['begin'];
					$te = $interval['end'];
					$sum += $te - $tb;
				} 
				catch(\Exception $e){ continue; }
			}
		}
//Yii::warning("\n******* SUM ****************************\n sum=".$sum."    ".(int)($sum/60/60)."\n");
		return $sum;		
	}

	//--------------------------------------------------------------------------------------
	//	Расчитать интервалы простоя, сложив прости в каждой заявке по лифту
	//--------------------------------------------------------------------------------------
	public function MakeReportTable($filter, $DateFrom, $DateTo)
	{
		$ReportTable = [];

		try{ $iDateFrom = strtotime( $DateFrom ); } catch(\Exception $e){ 
			return $ReportTable; 
		}
		try{ $iDateTo = strtotime( $DateTo ); } catch(\Exception $e){ 
			return $ReportTable; 
		}

/*
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
 		where tiequipment_id is not null $filter order by tiregion, tiequipment_id, tioosbegin ";
*/

		$sqltext="SELECT ticket.id, ticket.tiaddress, ticket.tiobjectcode, tiequipment_id, ticode, tiregion, tiopenedtime, tioosbegin, tioosend, tiplannedtimenew,  oostypetext, tiproblemtypetext, tidescription, tiproblemtext, streetname, fabuildingno, elporchno, elporchpos, elinventoryno 
		from ticket left join (
	SELECT e2.elnum, e2.worknum, p.*  FROM (SELECT a.sid, COUNT(a.sid) elnum, SUM(a.st) worknum
   FROM (SELECT CONCAT (e.elfacility_id, e.elporchno) sid, s.tiopstatus st, e.* FROM elevator e  INNER JOIN 
       (SELECT t.tiequipment_id, t.tiopstatus, MAX(t.tistatustime) lasttime, t.tifacility_id
           FROM ticket t WHERE t.tiequipment_id IN (SELECT id FROM elevator WHERE eldevicetype = 1)
                GROUP BY t.tiequipment_id) s ON e.id = s.tiequipment_id) a GROUP BY a.sid) e2 INNER JOIN
                 (SELECT CONCAT (e.elfacility_id, e.elporchno) sid, s.lasttime, s.tiopstatus st, e.*
                    FROM elevator e INNER JOIN (SELECT t.tiequipment_id, t.tiopstatus,
             MAX(t.tistatustime) lasttime, t.tifacility_id 
             FROM ticket t WHERE t.tiequipment_id IN (SELECT id FROM elevator WHERE eldevicetype = 1)
             GROUP BY t.tiequipment_id) s ON e.id = s.tiequipment_id) p ON  p.sid = e2.sid
		) el on ticket.tiequipment_id=el.id
 		left join ticketproblemtype on ticket.tiproblemtype_id =ticketproblemtype.id 
 		left join oostype on ticket.tioostype_id=oostype.id
 		left join facility on ticket.tifacility_id =facility.id 
 		left join street on facility.fastreet_id =street.id
 		where tiequipment_id is not null $filter order by tiregion, tiequipment_id, tioosbegin ";

//Yii::warning("\n---------------------------------SQL------------------\n".$sqltext.'\n');

		$tickets = Yii::$app->db->createCommand($sqltext)->queryAll();	

		$intervals = [];
		$ReportTableRec['tiequipment_id'] = 0;
		$start = true;
		foreach($tickets as $ticket){
			if ($ReportTableRec['tiequipment_id'] != $ticket['tiequipment_id']) {
				// следующий лифт
				if (!$start){
					// Делаем расчет и сохраняем запись по предидущему лифту
					//Yii::warning("\n=== CALC ================================\n eq_id:".$ReportTableRec['tiequipment_id']."\n eq_cod:".$ReportTableRec['tiobjectcode']."\n");
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
					'tioosbegin' => $ticket['tioosbegin'],
					'tickets' => [],
					'oosumtime' => 0,
					//'' => $ticket[''],
				];
				$start = false;
			}
			$ReportTableRec['tickets'] [] = $ticket['id'];
			$tbegin = $ticket['tioosbegin'];
			$tend = $ticket['tioosend'];
			if (empty($tbegin) and empty($tend)) continue;
			if (empty($tbegin)) $tbegin = $DateFrom;
			if (empty($tend)) $tend = $DateTo;
			try{ $ibegin = strtotime( $tbegin ); } catch(\Exception $e){ continue; }
			try{ $iend = strtotime( $tend ); } catch(\Exception $e){ continue; }
			if ($ibegin < $iDateFrom) $ibegin = $iDateFrom;
			if ($iend > $iDateTo) $iend = $iDateTo;
			while( isset($intervals[$ibegin]) ) $ibegin++;
			$intervals[$ibegin] = +1;
			while( isset($intervals[$iend]) ) $iend++;
			$intervals[$iend] =  -1;
	Yii::warning("\n--- int ext ---------------------------------------\n [".$ticket['tioosbegin']."]: ".date('d-m-Y H:i:s',$ibegin)."  = ".$ibegin." \n [".$ticket['tioosend']."]: ".date('d-m-Y H:i:s',$iend)."  = ".$iend."\n  eq_id:".$ticket['tiequipment_id']."\n  eq_cod:".$ticket['tiobjectcode']."\n");

		}	// foreach

		if (!$start){
			// Делаем расчет и сохраняем последнюю запись
			//Yii::warning("\n=== CALC END ============================\n eq_id:".$ReportTableRec['tiequipment_id']."\n eq_cod:".$ReportTableRec['tiobjectcode']."\n");
			$sumtime = self::CalcIntervalsSum( $intervals );
			$ReportTableRec['oosumtime'] = (int)($sumtime/60/60);
			$ReportTable[] = $ReportTableRec;
		}
		
		return $ReportTable;
	}

//--------------------------------------------------------------------------------------
//	Получить параметры лифтов, которые есть в отчете
//--------------------------------------------------------------------------------------
	function GetElevatorsParams($ReportTable)
	{
		$ellist = "(0";
		$first = true;
		foreach($ReportTable as $rec)
		{
			if (!empty($rec['tiequipment_id'])) 
				$ellist .= ",".$rec['tiequipment_id'];
		}
		$ellist .= ")";
		$sql = "SELECT * from (
SELECT e2.elnum, e2.worknum, p.*  FROM (SELECT a.sid, COUNT(a.sid) elnum, SUM(a.st) worknum
   FROM (SELECT CONCAT (e.elfacility_id, e.elporchno) sid, s.tiopstatus st, e.* FROM elevator e  INNER JOIN 
       (SELECT t.tiequipment_id, t.tiopstatus, MAX(t.tistatustime) lasttime, t.tifacility_id
           FROM ticket t WHERE t.tiequipment_id IN (SELECT id FROM elevator WHERE eldevicetype = 1)
                GROUP BY t.tiequipment_id) s ON e.id = s.tiequipment_id) a GROUP BY a.sid) e2 INNER JOIN
                 (SELECT CONCAT (e.elfacility_id, e.elporchno) sid, s.lasttime, s.tiopstatus st, e.*
                    FROM elevator e INNER JOIN (SELECT t.tiequipment_id, t.tiopstatus,
             MAX(t.tistatustime) lasttime, t.tifacility_id 
             FROM ticket t WHERE t.tiequipment_id IN (SELECT id FROM elevator WHERE eldevicetype = 1)
             GROUP BY t.tiequipment_id) s ON e.id = s.tiequipment_id) p ON  p.sid = e2.sid
        ) x where x.id in ".$ellist." ;";
		$elparams = Yii::$app->db->createCommand($sql)->queryAll();	
		
		return $elparams;
	}

//--------------------------------------------------------------------------------------
// Добавить в таблицу отчета параметры лифта	
//--------------------------------------------------------------------------------------
	function AddElPArams(&$ReportTable, $ElParams)
	{
		foreach($ReportTable as &$rec){
			$elevator = NULL;
			foreach($ElParams as $elrec){
				if ($elrec['id'] == $rec['tiequipment_id'])
					$elevator = $elrec;
			}
			if (!empty($elevator)) {
				$rec['ep_status'] = $elevator['st'];
				$rec['ep_statustime'] = $elevator['lasttime'];
				$rec['ep_elnum'] = $elevator['elnum'];
				$rec['ep_worknum'] = $elevator['worknum'];
			}
		}
	}

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
	public function generateReport($params)
	{
 		$filter = $this->FillFilterDate();

		$ReportTable = $this->MakeReportTable($filter, $this->datefrom, $this->dateend);

		$this->ElParams = $this->GetElevatorsParams($ReportTable);
		$this->AddElPArams($ReportTable, $this->ElParams);

		$Report = [];
		$ReportLine = ['District'=>"",'e0'=>0, 'e1'=>0, 'e2'=>0,'h0'=>0, 'h1'=>0, 'h2'=>0];
		$SummLine = ['District'=>"ИТОГО",'e0'=>0, 'e1'=>0, 'e2'=>0,'h0'=>0, 'h1'=>0, 'h2'=>0, 'total'=>true];
		$District = NULL;
		$count = 0;
		foreach($ReportTable as $rec){
			$count++;

			if (is_null($District) || ($District != $rec['tiregion'])) {
				if (!is_null($District)) {
					$Report[$District] = $ReportLine;
					$SummLine['e0'] += $ReportLine['e0'];
					$SummLine['e1'] += $ReportLine['e1'];
					$SummLine['e2'] += $ReportLine['e2'];
					$SummLine['h0'] += $ReportLine['h0'];
					$SummLine['h1'] += $ReportLine['h1'];
					$SummLine['h2'] += $ReportLine['h2'];
				}
				$District = $rec['tiregion'];
				$ReportLine = ['District'=>$District, 'e0'=>0, 'e1'=>0, 'e2'=>0,'h0'=>0, 'h1'=>0, 'h2'=>0];
			}
			switch($rec['ep_status']){
				case '0': $ReportLine['e0']++; $ReportLine['h0'] += $rec['oosumtime']; break;
				case '1': $ReportLine['e1']++; $ReportLine['h1'] += $rec['oosumtime']; break;
				default : $ReportLine['e2']++; $ReportLine['h2'] += $rec['oosumtime']; break;
			}
		}
		$Report[$District] = $ReportLine;
		$SummLine['e0'] += $ReportLine['e0'];
		$SummLine['e1'] += $ReportLine['e1'];
		$SummLine['e2'] += $ReportLine['e2'];
		$SummLine['h0'] += $ReportLine['h0'];
		$SummLine['h1'] += $ReportLine['h1'];
		$SummLine['h2'] += $ReportLine['h2'];
		$Report[] = $SummLine;

		$provider = new ArrayDataProvider([
			'allModels' => $Report,
			'key' => 'District',
		]);
		return $provider;	
	}

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
	public function generateList($params)
	{
 		$filter = $this->FillFilter();

		$ReportTable = $this->MakeReportTable($filter, $this->datefrom, $this->dateend);

		if (empty($this->ElParams)) 
			$this->ElParams = $this->GetElevatorsParams($ReportTable);
		$this->AddElPArams($ReportTable, $this->ElParams);

		if (empty($this->reportpagesize)) 
			$rpp = 10;
		else
			$rpp = $this->reportpagesize;

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
			'pagination'=>['pageSize' => $rpp],
		]);
		return $provider;	
	}


}

