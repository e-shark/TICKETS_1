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

	// Получить список районов
	public static function getTiRegions()
	{
		$vtiRegions = Yii::$app->db->createCommand('SELECT districtname, districtcode FROM district where districtlocality_id=159;')->queryAll();	
		return $vtiRegions;
	}

	// Получить список улиц для района (по коду района)
	//	RegionCode - Код района. Если код района не задан, дает список всех улиц 
	//  f_all - добавить в список пункт 'все' (для формирования списков выбора)
	public static function getStreetsList( $RegionCode = 0 , $f_all=false)
	{
		//$RegionName = Yii::$app->db->createCommand('SELECT districtname FROM district where districtlocality_id=159 and districtcode ='.$RegionCode.';')->queryOne()["districtname"];	
		//$vStreets =  Yii::$app->db->createCommand('SELECT id, streetname as text FROM street where streetdistrict like "'.$RegionName.'";')->queryAll();	
		$sql = "SELECT distinct street.id, concat(' ',ifnull(street.streettype,''),' ', ifnull(street.streetname,'')) as text FROM facility
				left join district on facility.fadistrict_id= district.id
				left join street on facility.fastreet_id=street.id 
				".( empty($RegionCode) ? "" : "where district.districtcode = ".$RegionCode )."
				order by streetname ";
		$vStreets =  Yii::$app->db->createCommand($sql)->queryAll();	
		if ($f_all) array_unshift($vStreets, ['id'=>0,'text'=>'все']);	// вставляем запись "все" в начало массива
		return $vStreets;
	}

	// Получить список улиц для района (по ID района)
	//	RegionCode - ID района. Если ID района не задан, дает список всех улиц 
	//  f_all - добавить в список пункт 'все' (для формирования списков выбора)
	public static function getStreetsList2( $RegionID = 0 , $f_all=false)
	{
		//$RegionName = Yii::$app->db->createCommand('SELECT districtname FROM district where districtlocality_id=159 and id ='.$RegionID.';')->queryOne()["districtname"];	
		//$vStreets =  Yii::$app->db->createCommand('SELECT id, streetname as text FROM street where streetdistrict like "'.$RegionName.'";')->queryAll();	
		$sql = "SELECT distinct street.id, concat(' ',ifnull(street.streettype,''),' ', ifnull(street.streetname,'')) as text FROM facility
				left join district on facility.fadistrict_id= district.id
				left join street on facility.fastreet_id=street.id 
				".( empty($RegionID) ? "" : "where district.id = ".$RegionID )."
				order by streetname ";
		$vStreets =  Yii::$app->db->createCommand($sql)->queryAll();	
		if ($f_all) array_unshift($vStreets, ['id'=>0,'text'=>'все']);	// вставляем запись "все" в начало массива
		return $vStreets;
	}

	// Получить список домов на улице
	//	StreetID - ID улицы
	//  f_all - добавить в список пункт 'все' (для вормирования списков выбора)
	public static function getFacilitiesList( $StreetID = 0, $f_all=false)
	{
		if (empty($StreetID)) $StreetID = 0;
		$vStreets =  Yii::$app->db->createCommand('SELECT facility.id, coalesce(concat(faaddressno," ","сек.",fasectionno),faaddressno) as text FROM street join facility on fastreet_id = street.id where street.id ='.$StreetID.';')->queryAll();	
		if ($f_all) array_unshift($vStreets, ['id'=>0,'text'=>'все']);	// вставляем запись "все" в начало массива
		return $vStreets;
	}

	// Получить список возможных неисправностей
	public static function getProblemsList( $ObjectId = 0)
	{
		$ObjectName =  Yii::$app->db->createCommand('SELECT tiobject, tiobjectcode FROM ticketobject WHERE tiobjectcode = '.$ObjectId.';')->queryOne()['tiobject'];
		$Problems =  Yii::$app->db->createCommand('SELECT id, tiproblemtypetext, tiproblemtypecode FROM ticketproblemtype WHERE tiproblemtypetext like "%'.$ObjectName.'%";')->queryAll();	
		$res = Html::dropDownList('tiProblem', 'null', ArrayHelper::map($Problems,'id','tiproblemtypetext'),['id'=>'ProblemSelect','class'=>'form-control']); //'onChange' => 'onSelectProblem'
		return $res;
	}

	// Получить кол-во подъездов в доме
	public static function getEntranceNumber($FacilityId = 0)
	{
   		$Sel =  Yii::$app->db->createCommand('SELECT faporchesnum FROM facility WHERE id = :fid ;')->bindValues([':fid'=>$FacilityId])->queryOne()['faporchesnum'];		
   		if (!empty($Sel)) return (0+$Sel);
   		else return 0;
	}

	// Получить список подъездов с лифтами
	public static function getEntranceWithElevators( $FacilityId = 0, $ObjectId = '000')
	{
   		$Sel =  Yii::$app->db->createCommand('SELECT  elporchno as id, elporchno as text FROM elevator e left join ticketobject o on e.eldevicetype = o.tiobjectdevicetype WHERE elfacility_id = :fid and o.tiobjectcode = :objid group by elporchno;')->bindValues([':fid'=>$FacilityId, ':objid'=>$ObjectId])->queryAll();		
   		$cnt = count($Sel);
   		if ($cnt > 0){
   			if (1 == $cnt){
   				//$input = Html::input('text','tiEntrance',$Sel[0]['id'],['id'=>'tiEntranceInput','class'=>'form-control', 'disabled'=>'true' ]);
   				$input = Html::hiddenInput('tiEntrance', $Sel[0]['id'],['id'=>'tiEntranceInput']).Html::input('text','tiEntrance_ex',$Sel[0]['id'],['id'=>'tiEntranceInput_ex','class'=>'form-control', 'disabled'=>'true' ]);
   			} else{
				$input =Html::dropDownList('tiEntrance', 'null', ArrayHelper::map($Sel,'id','text'),['id'=>'tiEntranceInput','class'=>'form-control','onChange'=>'onSelectEntrance()']) ;
   			}

   		} else{
   			$input = Html::input('text','tiEntrance','',['id'=>'tiEntranceInput','class'=>'form-control']);
   		};
   		return $input ;
	}

	// Получить список лифтов в доме
	// 	FacilityId - ID дома
	//  EntranceId - номер (буква) подъезда
	public static function getElevatorsList( $FacilityId = 0, $EntranceId=0)
	{
		$Elevators =  Yii::$app->db->createCommand('SELECT id, concat(ifnull(elporchpos,"")," ", ifnull(eltype,"")) as text FROM elevator WHERE elfacility_id = '.$FacilityId.' and elporchno = '.$EntranceId.' and eldevicetype = 1;')->queryAll();
		$res['Elevators'] = Html::dropDownList('tiElevator', 'null', ArrayHelper::map($Elevators,'id','text'),['id'=>'tiElevatorSelect','class'=>'form-control','onChange'=>'onSelectElevator()']);
		$res['ElNum'] = count($Elevators);
		return $res;
	}

	// Получить список щитовых в доме
	// 	FacilityId - ID дома
	//  EntranceId - номер (буква) подъезда
	public static function getSwichboardList( $FacilityId = 0, $EntranceId=0)
	{
		$Elevators =  Yii::$app->db->createCommand('SELECT id, concat("№",ifnull(elinventoryno,"")) as text FROM elevator WHERE elfacility_id = '.$FacilityId.' and elporchno = '.$EntranceId.' and eldevicetype = 10;')->queryAll();
		$res['Elevators'] = Html::dropDownList('tiElevator', 'null', ArrayHelper::map($Elevators,'id','text'),['id'=>'tiElevatorSelect','class'=>'form-control','onChange'=>'onSelectElevator()']);
		$res['ElNum'] = count($Elevators);
		return $res;
	}


	// Получить список монтеров
    public static function getFittersList()
    {
		return  Yii::$app->db->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as text FROM employee WHERE oprights LIKE "%F%" ORDER BY lastname;')->queryAll();	
    }

	// Получить список монтеров с прикрепленными щитовыми
    public static function getFittersWithSBList()
    {
    	$sql = 'SELECT el.elperson_id as id, 
				concat( ifnull(emp.lastname,"")," ",ifnull(emp.firstname,"")," ",ifnull(emp.patronymic,"")) as text
				from employee emp, elevator el  
				where emp.id = el.elperson_id
				  and el.eldevicetype = 10
				group by  el.elperson_id 
				order by emp.lastname;';
		return  Yii::$app->db->createCommand($sql)->queryAll();	
    }

	// Получить список исполнителей подразделения
    public static function getExecutantsList($DivisionID)
    {
		return  Yii::$app->db->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as text FROM employee WHERE division_id = '.$DivisionID.' ORDER BY lastname;')->queryAll();	
    }

	// Получить список исполнителей для ЛАС
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
    	$divigions = Yii::$app->db->createCommand('SELECT d.id,d.divisionname from employee e join division d on e.division_id=d.id where e.oprights like"%M%" and d.divisioncodesvc like "%L%";')->queryAll();
		return  $divigions;
	}

    public static function getExecutantsListVDESForLAS()
    {
		$DivisionID =  Yii::$app->db->createCommand('SELECT id FROM division WHERE divisioncode = 12;')->queryOne()['id'];	
        $res = Yii::$app->db->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as text FROM employee WHERE division_id = '.$DivisionID.' ORDER BY lastname;')->queryAll();			
        return $res;
    }

	public static function getDivisionsListVDESForMaster()
	{
    	$divigions = Yii::$app->db->createCommand('SELECT d.id,d.divisionname from employee e join division d on e.division_id=d.id where e.oprights like"%M%" and d.divisioncodesvc like "%E%";')->queryAll();
    	return  $divigions;
	}


	public static function getElevatorDivision($ElevatorId, $ObjectId)
	{
		$res=[];
		$devtype = 'E';
		if ('002'==$ObjectId) $devtype = 'L';
    	$eldivigion = Yii::$app->db->createCommand('SELECT elevator.id, elevator.eldivision_id, division.id as divid, division.divisionname as divname FROM elevator join division on elevator.eldivision_id = division.id where elevator.id = '.$ElevatorId.' ;')->queryOne();
    	if (!is_null( $eldivigion['divid'])) {$res['DivId'] = $eldivigion['divid'];}
    	if (!is_null( $eldivigion['divname'])) {$res['DivName'] = $eldivigion['divname'];}
    	else {$res['DivName'] = "";}
		return  $res;
	}

}
