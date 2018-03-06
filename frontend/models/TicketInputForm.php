<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use zxbodya\yii2\galleryManager\GalleryBehavior;

class TicketInputForm extends Model
{
	public $tiObjects;
	public $tiProblems;
	public $tiStatuses;
	public $tiRegions;
	public $tiExecutantsLas;
	public $tiDepsList;


	public static function getTiObjects()
	{
		$vtiObjects = Yii::$app->db->createCommand('SELECT tiobject, tiobjectcode FROM ticketobject')->queryAll();	
		return $vtiObjects;
	}

	public static function getTiProblems()
	{
		$vtiProblems = Yii::$app->db->createCommand('SELECT tiproblemtypetext, tiproblemtypecode FROM ticketproblemtype')->queryAll();	
		return $vtiProblems;
	}

	public static function getTiRegions()
	{
		$vtiRegions = Yii::$app->db->createCommand('SELECT districtname, districtcode FROM district where districtlocality_id=159;')->queryAll();	
		return $vtiRegions;
	}

	public static function getStreetsList( $GerionID = 0)
	{
		$RegionName = Yii::$app->db->createCommand('SELECT districtname FROM district where districtlocality_id=159 and districtcode ='.$GerionID.';')->queryOne()["districtname"];	
		$vStreets =  Yii::$app->db->createCommand('SELECT id, streetname as text FROM street where streetdistrict like "'.$RegionName.'";')->queryAll();	
		return $vStreets;
	}

	public static function getFacilitysList( $StreetID = 0)
	{
		$vStreets =  Yii::$app->db->createCommand('SELECT facility.id, facility.faaddressno as text FROM street join facility on   fastreet_id = street.id where street.id ='.$StreetID.';')->queryAll();	
		return $vStreets;
	}

	public static function getProblemsList( $ObjectId = 0)
	{
		$ObjectName =  Yii::$app->db->createCommand('SELECT tiobject, tiobjectcode FROM ticketobject WHERE tiobjectcode = '.$ObjectId.';')->queryOne()['tiobject'];
		$Problems =  Yii::$app->db->createCommand('SELECT id, tiproblemtypetext, tiproblemtypecode FROM ticketproblemtype WHERE tiproblemtypetext like "%'.$ObjectName.'%";')->queryAll();	
		$res = Html::dropDownList('tiProblem', 'null', ArrayHelper::map($Problems,'id','tiproblemtypetext'),['id'=>'ProblemSelect','class'=>'form-control']); //'onChange' => 'onSelectProblem'
		return $res;
	}

	public static function getEntranceWithElevators( $FacilityId = 0)
	{
   		//$Sel =  Yii::$app->db->createCommand('SELECT  elporchno as id, concat("П. ", ifnull(elporchno,"")) as text FROM elevator WHERE elfacility_id = :fid group by elporchno;')->bindValues([':fid'=>$FacilityId])->queryAll();		
   		$Sel =  Yii::$app->db->createCommand('SELECT  elporchno as id, elporchno as text FROM elevator WHERE elfacility_id = :fid group by elporchno;')->bindValues([':fid'=>$FacilityId])->queryAll();		
   		$cnt = count($Sel);
   		if ($cnt > 0){
   			if (1 == $cnt){
   				$input = Html::input('text','tiEntrance',$Sel[0]['id'],['id'=>'tiEntranceInput','class'=>'form-control' ,'disabled'=>'true' ]);
   			} else{
				$input =Html::dropDownList('tiEntrance', 'null', ArrayHelper::map($Sel,'id','text'),['id'=>'tiEntranceInput','class'=>'form-control','onChange'=>'onSelectEntrance()']) ;
   			}

   		} else{
   			$input = Html::input('text','tiEntrance','',['id'=>'tiEntranceInput','class'=>'form-control']);
   		};
   		return $input ;
	}

	public static function getElevatorsList( $FacilityId = 0, $EntranceId=0)
	{
		$Elevators =  Yii::$app->db->createCommand('SELECT id, concat(ifnull(elporchpos,"")," ", ifnull(eltype,"")) as text FROM elevator WHERE elfacility_id = '.$FacilityId.' and elporchno = '.$EntranceId.';')->queryAll();

		$res['Elevators'] = Html::dropDownList('tiElevator', 'null', ArrayHelper::map($Elevators,'id','text'),['id'=>'tiElevatorSelect','class'=>'form-control','onChange'=>'onSelectElevator()']);
		$res['ElNum'] = count($Elevators);
		return $res;
	}

    public static function getExecutantsList($DivisionID)
    {
		return  Yii::$app->db->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as text FROM employee WHERE division_id = '.$DivisionID.';')->queryAll();	
    }

	public static function getExecutantsListForLAS()
	{
		$res = [];
		$DivisionID = 8;
		$DivisionID =  Yii::$app->db->createCommand('SELECT id FROM division WHERE divisioncode = 8;')->queryOne()['id'];	
		$res = TicketInputForm::getExecutantsList($DivisionID);
		return $res;
	}

	public static function getDivisionsListForMaster()
	{
    	$divigions = Yii::$app->db->createCommand('select d.id,d.divisionname from employee e join division d on e.division_id=d.id where e.oprights like"%M%";')->queryAll();
		return  $divigions;
	}

	public static function getElevatorDivision($ElevatorId)
	{
		$res=[];
    	$eldivigion = Yii::$app->db->createCommand('SELECT elevator.id, elevator.eldivision_id, division.id as divid, division.divisionname as divname FROM elevator join division on elevator.eldivision_id = division.id where elevator.id = '.$ElevatorId.';')->queryOne();
    	if (!is_null( $eldivigion['divid'])) {$res['DivId'] = $eldivigion['divid'];}
    	if (!is_null( $eldivigion['divname'])) {$res['DivName'] = $eldivigion['divname'];}
    	else {$res['DivName'] = "";}
		return  $res;
	}

}
