<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

class Report_ElevatorsList extends Model
{

	public function generate($params)
	{
		$sqltext = "SELECT e.id , elremoteid, elfacility_id, d.districtname, s.streettype, s.streetname, f.faaddressno, e.elporchno, e.elporchpos, e.eltype, e.elload, e.elspeed, e.elstops, e.elregyear, v.divisionname  FROM elevator e 
			left join facility f on f.id=e.elfacility_id
			left join district d on d.id=f.fadistrict_id
			left join street s on s.id=f.fastreet_id
			left join division v on v.id=e.eldivision_id";
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $provider;	
	}

}

