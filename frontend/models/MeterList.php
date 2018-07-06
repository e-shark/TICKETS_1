<?php

namespace frontend\models;

use yii\base\Model;
use frontend\models\Meter;
use yii\data\SqlDataProvider;

class MeterList extends Model
{
	public function GetMeterList()
	{


		$sqltext = "select pm.*,  md.mdatatime, md.mdata,
  fa.id as facility_id, st.streettype, st.streetname, fa.faaddressno, 
  concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(pm.meterporchno,0), concat(' п.',pm.meterporchno),'') ) as addrstr 
from powermeter pm
left join (
 select powermeter.id ,powermeterdata.mdatatime, powermeterdata.mdatacode, powermeterdata.mdata  
 from powermeter 
 join powermeterdata on powermeterdata.mdatameter_id = powermeter.id 
 join (select max(mdatatime) as maxt from powermeterdata where mdatadeltime is null group by mdatameter_id ) m2 on m2.maxt =mdatatime
) md on md.id = pm.id
join facility fa on fa.id = pm.meterfacility_id 
join street st on st.id=fa.fastreet_id ";



		$provider = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $provider;		
	}
}