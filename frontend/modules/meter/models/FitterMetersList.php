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
		if( FALSE === strpos($this->oprights['oprights'],'F' ) ) {
			$this->fitter = $this->oprights['id'];
		}
	}

	// Заполняем модель данными из запроса
	// и формирует строку фильтра для запроса SELECT
	public static function FillFilterParams( &$model, $params)
	{
		Yii::warning("************************************************queryParams***********************[\n".json_encode($params)."\n]");

		//--- механик, к которому закреплена шитовая, к которой относится счетчика
		if( array_key_exists('fitter',$model->attributes ) ) {
			if (!empty($params['fitter'])) {
				$model->fitter  =  $params['fitter'];
				$filtersql	.=" and fitter = ".$model->fitter;
			}
		}
		return $filtersql;

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

	public function GetMeterList()
	{
		$filter = FitterMetersList::FillFilterParams($this, Yii::$app->request->queryParams);
		$sqltext = 
"SELECT dd.*, 
		(select e.elperson_id from elevator e where e.eldevicetype = 10 and e.elfacility_id=dd.meterfacility_id limit 1) as fitter,
        concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(dd.meterporchno,0), concat(' ?.',dd.meterporchno),'') ) as addrstr ,
        a.A_mtime, a.A_mdata, a.A_mwho, a.A_mfile, a.A_mstate, a.A_mcomment, a.A_tid, -- A текущие
        b.B_mtime, b.B_mdata, b.B_mwho, b.B_mfile, b.B_mstate, b.B_mcomment, b.B_tid, -- В предыдущие
        c.C_mtime, c.C_mdata, c.C_mwho, c.C_mfile, c.C_mstate, c.C_mcomment, c.C_tid  -- С старые
 FROM  (SELECT * FROM powermeter p) dd
 LEFT OUTER JOIN (
	SELECT 	n.mdatameter_id A_id, n.mdatatime A_mtime, n.mdata A_mdata,
			(SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) FROM employee e, powermeterdata pm  WHERE pm.mdatawho=e.id AND  pm.id = gg.id ) A_mwho,
			n.mdatafile A_mfile, n.mdatameterstate A_mstate, n.mdatacomment A_mcomment, n.id A_tid, n.mdatadeltime, n.mdatacode
	FROM powermeterdata n,
    	(	SELECT MAX(pp.id) id 
      		FROM (	SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime > :DP2 AND mdatadeltime IS NULL ) pp, 
				 ( SELECT MAX(p.mdatatime) t, p.mdatameter_id id
							  FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime > :DP2 AND  mdatadeltime IS NULL) p 
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
    (SELECT MAX(pp.id) id FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND (mdatatime > :DP1 and mdatatime <= :DP2) AND mdatadeltime IS NULL ) pp, 
       (	SELECT MAX(p.mdatatime) t, p.mdatameter_id id
        	FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND (mdatatime > :DP1 and mdatatime <= :DP2) AND  mdatadeltime IS NULL) p 
        	GROUP BY p.mdatameter_id
        ) g 
       WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id) gg
   WHERE ppp.id = gg.id
) c ON  dd.id = c.C_id
JOIN facility fa on fa.id = dd.meterfacility_id 
JOIN street st on st.id=fa.fastreet_id  
ORDER BY 1 ";
		$sqltext = "SELECT s.* FROM ( ".$sqltext." ) s WHERE id>0 ".$filter ; 
		Yii::warning("************************************************SQL*******************************[\n".$sqltext."\n]");
		Yii::warning("************************************************filter****************************[\n".$filter."\n]");
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'params' => [
				':OBIS' => "1.8.0",
				':DP1' => '2018-06-10',
				':DP2' => "2018-07-10",
			],
		]);
		return $provider;		
	}

}