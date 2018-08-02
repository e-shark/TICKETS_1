<?php
namespace frontend\modules\meter\models;

use yii;
use yii\base\Model;
use frontend\models\Meter;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use frontend\models\Tickets;

class FitterMetersList extends Model
{

	public $assigned;
	public $fitter;
	public $district;
	public $street;
	public $facility;
	public $datapresent;
	public $oprights;

	function __construct( $config = []) {
		parent::__construct( $config);	
		$this->oprights = Tickets::getUserOpRights();
		if( FALSE !== strpos($this->oprights['oprights'],'F' ) ) {
            // определили, что пользователь - это механик
			$this->fitter = $this->oprights['id'];
		}
	}

	// Формирует строку фильтра для запроса SELECT
	// и з аполняем модель данными из запроса.
	// model - ссылка на модель (сюда загружаются значения из параметров запоса)
	// params - массив параметров запроса
	// isfitter - признак, что юзер - это механик
	public function FillFilterParams( &$model, $params, $isfitter = false)
	{
		//Yii::warning("************************************************model***********************[\n".json_encode($model)."\n]");
		//Yii::warning("************************************************params***********************[\n".json_encode($params)."\n]");

		if ($isfitter){
			if (!is_null($params['assigned'])) 
				$model->assigned  =  $params['assigned'];
			else{	
				//Флаг либо выключен, либо просто первый раз на странице
				//Если первый раз на странице, то не будет и другого параметра
				if (is_null($params['datapresent'])) 
					// значит первый раз на странице
					// Ставим значение флага по умолчанию
					$model->assigned  =  1;
			}	

			if ($model->assigned && (!empty($model->fitter)))
				$filtersql	.=" and fitter = ".$model->fitter;
		}else{
			if (!empty($params['fitter'])) {
				$model->fitter  =  $params['fitter'];
				$filtersql	.=" and fitter = ".$model->fitter;
			}
		}

		//--- Наличие текущих показаний 
		if (!empty($params['datapresent'])) {
			$model->datapresent = $params['datapresent'];
			switch($model->datapresent){
			case 1: $filtersql	.=" and A_mtime IS NOT NULL"; break;
			case 2: $filtersql	.=" and A_mtime IS NULL"; break;
			}
		}

		//--- Запоминаем поля адреса
		$model->facility = null;
		if (!empty($params['facility'])) {
			$model->facility = $params['facility'];
		}
		$model->street = null;
		if (!empty($params['street'])) {
			$model->street = $params['street'];
		}
		$model->district = null;
		if (!empty($params['district'])) {
			$model->district = $params['district'];
		}

		//-- Собираем фильтр адреса
		if(!empty($model->facility))
			$filtersql	.=" and meterfacility_id = ".$model->facility;
		else
			if(!empty($model->street))
				$filtersql	.=" and fastreet_id = ".$model->street;
			else	
				if(!empty($model->district))
					$filtersql	.=" and fadistrict_id = ".FitterMetersList::getRegionID($model->district);

		//Yii::warning("************************************************model***********************[\n".json_encode($model)."\n]");
		return $filtersql;

	}

	// Возвращает ID района по его коду
	public static function getRegionID($RegionCode)
	{
		$result = null;
		$sql = "SELECT * FROM district d where d.districtcode = :rcode ;";
		if( !empty($RegionCode))
			$res = Yii::$app->db->createCommand($sql)->bindValues([':rcode'=>$RegionCode])->queryOne()['id'];
		return $res;
	}

	// Возвращает код района, в котором находятся щитовые, закрепленные за механиком
	// (если закреплены щитовые из нескольких районов, возвращает первый попавшийся из них)
	public static function getFitterDistrictCodeBySB($FitterID)
	{
		$sql = "SELECT el.id, el.elfacility_id, fa.fadistrict_id, ds.districtcode from elevator el , facility fa, district ds where fa.id=el.elfacility_id and ds.id = fa.fadistrict_id and el.elperson_id = :fid;";
		return Yii::$app->db 	// may be FALSE, if user have not a corresponding record in employee table
        	->createCommand($sql)->bindValues([':fid'=>$FitterID])
        	->queryOne()['districtcode'];
	}

	// Возвращает список счетчиков
	// Использует фильтр, на основе параметров запроса
	public function GetMeterList($filter)
	{
		if (empty(Yii::$app->params['MeterAccauntingPeriodDayOfMonth'])) $dateperiod = 10;		// дата начала расчетного периода каждого месяца 
		else $dateperiod = Yii::$app->params['MeterAccauntingPeriodDayOfMonth'];
		$TS2 = Yii::$app->formatter->asDatetime( mktime(0, 0, 0, date("m"), $dateperiod, date("Y")) ,'yyyy-MM-dd H:i:s');
		if (date("d") < $dateperiod)
			$TS2 = Yii::$app->formatter->asDatetime( strtotime( $TS2." -1 month" ) ,'yyyy-MM-dd H:i:s');
		$TS1 = Yii::$app->formatter->asDatetime( strtotime( $TS2." -1 month" ) ,'yyyy-MM-dd H:i:s');

		$sqltext = 
"SELECT dd.*,
        (select e.elperson_id from elevator e where e.eldevicetype = 10 and e.elfacility_id=dd.meterfacility_id limit 1) as fitter,
        fastreet_id, fadistrict_id, 
        concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(dd.meterporchno,0), concat(' ?.',dd.meterporchno),'') ) as addrstr ,
        a.A_mtime, a.A_mdata, a.A_mwho, a.A_mfile, a.A_mstate, a.A_mcomment, a.A_tid,  -- A текущие
        c.C_mtime, c.C_mdata, c.C_mwho, c.C_mfile, c.C_mstate, c.C_mcomment, c.C_tid,  -- С предыдущие
        b.B_mtime, b.B_mdata, b.B_mwho, b.B_mfile, b.B_mstate, b.B_mcomment, b.B_tid   -- В старые
 FROM  (SELECT * FROM powermeter p) dd
 LEFT OUTER JOIN (
	SELECT 	n.mdatameter_id A_id, n.mdatatime A_mtime, n.mdata A_mdata,
			(SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) FROM employee e, powermeterdata pm  WHERE pm.mdatawho=e.id AND  pm.id = gg.id ) A_mwho,
			n.mdatafile A_mfile, n.mdatameterstate A_mstate, n.mdatacomment A_mcomment, n.id A_tid, n.mdatadeltime, n.mdatacode
	FROM powermeterdata n,
    	(	SELECT MAX(pp.id) id 
      		FROM (	SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime >= :DP2 AND mdatadeltime IS NULL ) pp, 
				 ( SELECT MAX(p.mdatatime) t, p.mdatameter_id id
							  FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime >= :DP2 AND  mdatadeltime IS NULL) p 
                              GROUP BY p.mdatameter_id
                 ) g 
			WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id
    	) gg
	WHERE n.id = gg.id 
) a ON  dd.id = a.A_id 
LEFT OUTER JOIN (
   SELECT ppp.mdatameter_id B_id, ppp.mdatatime B_mtime, ppp.mdata B_mdata,
    (SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) FROM employee e, powermeterdata pm  WHERE pm.mdatawho=e.id AND  pm.id = gg.id ) B_mwho,
      ppp.mdatafile B_mfile, ppp.mdatameterstate B_mstate, ppp.mdatacomment B_mcomment, ppp.id B_tid, ppp.mdatadeltime, ppp.mdatacode
  FROM powermeterdata ppp,
    (SELECT MAX(pp.id) id FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime < :DP1 AND mdatadeltime IS NULL ) pp, 
       (SELECT MAX(p.mdatatime) t, p.mdatameter_id id
        FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime < :DP1 AND  mdatadeltime IS NULL) p 
        GROUP BY p.mdatameter_id) g 
       WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id) gg
  WHERE ppp.id = gg.id
) b  ON dd.id = b.B_id
LEFT OUTER JOIN (
   SELECT ppp.mdatameter_id C_id, ppp.mdatatime C_mtime, ppp.mdata C_mdata,     (SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) FROM employee e, powermeterdata pm  WHERE pm.mdatawho=e.id AND  pm.id = gg.id ) C_mwho, ppp.mdatafile C_mfile, ppp.mdatameterstate C_mstate, ppp.mdatacomment C_mcomment, ppp.id C_tid, ppp.mdatadeltime, ppp.mdatacode
   FROM powermeterdata ppp,
    (SELECT MAX(pp.id) id FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND (mdatatime >= :DP1 and mdatatime < :DP2) AND mdatadeltime IS NULL ) pp, 
       (	SELECT MAX(p.mdatatime) t, p.mdatameter_id id
        	FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND (mdatatime >= :DP1 and mdatatime < :DP2) AND  mdatadeltime IS NULL) p 
        	GROUP BY p.mdatameter_id
        ) g 
       WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id) gg
   WHERE ppp.id = gg.id
) c ON  dd.id = c.C_id
JOIN facility fa on fa.id = dd.meterfacility_id 
JOIN street st on st.id=fa.fastreet_id  
ORDER BY 1 ";
		$sqltext = "SELECT s.* FROM ( ".$sqltext." ) s WHERE id>0 ".$filter ; 
		//Yii::warning("************************************************SQL*******************************[\n".$sqltext."\n]");
		//Yii::warning("************************************************filter****************************[\n".$filter."\n]");
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'params' => [
				':OBIS' => "1.8.0",
				':DP1' => $TS1,
				':DP2' => $TS2,
			],
		]);
		return $provider;		
	}

}