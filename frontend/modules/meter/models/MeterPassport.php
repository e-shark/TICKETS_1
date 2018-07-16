<?php

namespace frontend\modules\meter\models;

class MeterPassport extends Model
{

    public $id;
    public $metermodel;					// VARCHAR
    public $meterserialno;				// VARCHAR
    public $meterdigits;				// INT
    public $meterphases;				// INT

    public $metecalibrationinterval;	// INT
    public $metercurrent;				// INT
    public $metermaxcurrent;			// INT
    public $metervoltage;				// INT
    public $metercomno;					// INT

    public $metersysno;					// VARCHAR
    public $meterimei;					// VARCHAR
    public $meterphone;					// VARCHAR
    public $meterip;					// VARCHAR
    public $meterinventoryno;			// VARCHAR

    public $meteraccno;					// VARCHAR
    public $meteraccname;				// VARCHAR
    public $meterowner;					// VARCHAR
    public $meterdescr;					// VARCHAR
    public $meterloaddescr;				// VARCHAR

    public $meterporchno;				// VARCHAR
    public $meterfacility_id;			// INT

    public function FillFields($MeterId=null)
    {
    	if (!empty($MeterId)) $id = $MeterId;
    	if (empty($id)){

    	}else{
			$sqltext = "SELECT pm.* ,
			concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(pm.meterporchno,0), concat(' п.',pm.meterporchno),'') ) as addrstr 
			FROM powermeter pm
			join facility fa on fa.id = pm.meterfacility_id 
			join street st on st.id=fa.fastreet_id
			WHERE pm.id=".($id)." ;";
			$rec = Yii::$app->db->createCommand($sqltext)->queryOne();	
			if (!empty($rec)){
				$metermodel = $rec['metermodel'];
			}
    	}
    }
}
