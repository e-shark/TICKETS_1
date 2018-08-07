<?php
namespace frontend\modules\meter\models;

use yii;
use yii\base\Model;
use frontend\models\Meter;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;


class MetersList extends Model
{

	public $datefrom;	
	public $dateto;
	public $address;
	//public $dateless;	
	public $serial;	
	public $type;	
	public $monthloadfrom;

	public static function FillFilterParams( &$model, $params)
	{
		$filtersql = "";
		//---Preparу sql  statement for serial
		if( array_key_exists('serial',$model->attributes ) ) {
			if (!empty($params['serial'])) {
				$model->serial  =  $params['serial'];
				$filtersql	.=" and (meterserialno LIKE '%".$model->serial."%') ";
			}
		}
		//---Prepare sql  statement for type of meter
		if( array_key_exists('type',$model->attributes ) ) if( !empty($params['type'] ) ) {
            $model->type = $params['type'];
        	$filtersql .= " and (metermodel like '%$model->type%') "; 
		}
		//---Preparу sql  statement for datefrom
		if( array_key_exists('datefrom',$model->attributes ) ) {
			$model->datefrom = $params['datefrom'];
			if (!empty($params['datefrom'])) {
				try{$dateiso=Yii::$app->formatter->asDate($model->datefrom,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=null;}
				$model->datefrom = $dateiso;
				$filtersql	.=" and (mdatatime>'$model->datefrom') ";
			}
		}
		//---Preparу sql  statement for dateto
		if( array_key_exists('dateto',$model->attributes ) ) {
			$model->dateto   = empty($params['dateto']) ?  date('d-M-y') : $params['dateto'];
			try{$dateiso=Yii::$app->formatter->asDate($model->dateto,'yyyy-MM-dd');}catch(\Exception $e){ $dateiso=date('d-M-y'); }
			$model->dateto = $dateiso;
			if($model->dateto<$model->datefrom)$model->dateto=$model->datefrom;
			if (empty($params['datefrom']))
				$filtersql	.=" and (isnull(mdatatime) or (mdatatime<='$model->dateto  23:59:59')) ";
			else
				$filtersql	.=" and (mdatatime<='$model->dateto  23:59:59') ";
		}
		//---Prepare sql  statement for additional query string [code,1562 code,lift code,address]
		if( array_key_exists('address',$model->attributes ) ) if( !empty($params['address'] ) ) {
            $model->address = $params['address'];
            $fstrar = explode(' ',$model->address);
            for($i=0;$i<count($fstrar);$i++)if($i+1<count($fstrar))$fstrar[$i]=$fstrar[$i].'%';
            $fstr=implode( $fstrar );
        	$filtersql .= " and (addrstr like '%$fstr%') "; 
		}
		return $filtersql;
	}

	public function GetMeterList()
	{

		$filter = MetersList::FillFilterParams($this, Yii::$app->request->queryParams);
		$sqltext = "SELECT * from (select pm.*,  md.mdatatime, md.mdata,
  fa.id as facility_id, st.streettype, st.streetname, fa.faaddressno, 
  concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(pm.meterporchno,0), concat(' п.',pm.meterporchno),'') ) as addrstr 
from powermeter pm
left join (
SELECT ppp.id, ppp.mdatatime, ppp.mdata, ppp.mdatameter_id, ppp.mdatadeltime, ppp.mdatacode
  FROM powermeterdata ppp,
    (SELECT MAX(pp.id) id FROM (SELECT * FROM powermeterdata WHERE mdatacode = '1.8.0' AND mdatadeltime IS NULL ) pp, 
       (SELECT MAX(p.mdatatime) t, p.mdatameter_id id
        FROM (SELECT * FROM powermeterdata WHERE mdatacode = '1.8.0' AND  mdatadeltime IS NULL) p 
        GROUP BY p.mdatameter_id) g 
       WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id) gg
  WHERE ppp.id = gg.id 
     ORDER BY ppp.mdatameter_id, ppp.mdatatime
) md on md.mdatameter_id = pm.id
join facility fa on fa.id = pm.meterfacility_id 
join street st on st.id=fa.fastreet_id ";
		$sqltext .=") ms where (id>0) ".$filter;
		//Yii::warning("********************************************************************************[\n".$sqltext."\n]");
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $provider;		
	}

	public function GetMeterTypesList()
	{
    	$metertypes = ArrayHelper::map(Yii::$app->db->createCommand('SELECT DISTINCT metermodel FROM powermeter order by metermodel')->queryAll(),'metermodel','metermodel');
    	return $metertypes = [""=>'Все']+$metertypes;
	}
}