<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\TicketAction;

class TicketAddData extends Model
{
	public $recid = NULL;
    public $ticode;		//  CHAR(50), -- Ticket registration number
    public $ticodeex;	//  CHAR(50), -- Ticket alternative registration number
    public $tipriority; //	CHAR(20), -- Ticket priority, from highest
    public $tistatus;	//  CHAR(50), -- Status of the ticket, see description below
    public $tistatustime;	// TIMESTAMP  NULL,-- Date & time of last event of status change
    public $tiexecutantread;// CHAR(1), -- Flag, should be set after executant has read the ticket after new message for him arrived, be reset on placing new message for xecutant

    public $tiincidenttime;	// TIMESTAMP  NULL, -- Date & time the incident for which ticket was created
    public $tiopenedtime;	// TIMESTAMP  NULL, -- Date & time when ticket was inserted in db
    public $tiplannedtime;	// TIMESTAMP  NULL, -- Initial planned date & time up to which the normal operation should be restored
    public $tiplannedtimenew;	// TIMESTAMP  NULL, -- New planned date & time up to which the normal operation should be restored 
    public $tiiplannedtime;	// TIMESTAMP  NULL, -- Inner planned date & time up to which the normal operation should be restored (Master appoint job for fitter to this time)
    public $tisplannedtime;	// TIMESTAMP  NULL, -- Planned date & time up to which the spare parts should be supplied
    public $ticlosedtime;	// TIMESTAMP  NULL, -- Date & time when the ticket was closed

    public $tiobject_id;	//  INT,  -- object of incident
    public $tiproblemtype_id;	// INT,  -- incident problem type id from caller
    public $tiproblemtext;	// VARCHAR(200), -- incident type additional precise description
    public $tidescription;	// VARCHAR(255), -- incident description (for example: intrusion to machinery room , emergency button of the lift cabin)

    public $tifacility_id;  // INT,     -- id of facility 
    public $tifacilitycode;	// CHAR(20), -- code of facility where incident took place (for example the building's inventory number)
    public $tiobjectcode;	// CHAR(20), -- code of object in which the incident took place (for example the elevator's inventory(or serial) number)
    public $tiequipment_id; // INT, -- - id of object  in which the incident took place
    public $tiregion;	//  CHAR(50), -- code of region (or district) where the incident took place
    public $tiaddress;	//  VARCHAR(255), -- address string for the facility where incident took place

    public $tioriginator;	// VARCHAR(100), -- name of the person who placed ticket into db
    public $tioriginator_id;

    public $ticaller;	//  VARCHAR(100), -- Trouble call caller name
    public $ticallerphone;	// CHAR(16), -- Trouble call caller phone number
    public $ticalleraddress;	// VARCHAR(200), -- Trouble call caller address
    public $ticalltype;	//  CHAR(50), -- Trouble call type, (phone,emergency button,inner dispatcher line, etc)

    public $tiresumedtime;	// TIMESTAMP NULL, -- Date & time the resuming of normal operation
    public $tiresulterrorcode;	// CHAR(10), -- Resulting error code of incident reported by field service
    public $tiresulterrortext;	// VARCHAR(255), -- Resulting report on failure

    public $tioriginatordesk_id;
    public $tidesk_id;
    public $tidivision_id;	// INT NULL, -- sevice division id
    public $tiexecutant_id;	// INT NULL, -- sevice man id


    public function TicketAddNew()
    {
    	$res = NULL;
    	$this->ticode = TicketAddData::getGetTicketId();

		Yii::$app->db->createCommand()->insert('ticket',[
			'ticode'          => $this->ticode,
			'ticodeex'        => $this->ticodeex,
			'tipriority'      => $this->tipriority,
			'tistatus'        => $this->tistatus,
			'tistatustime'    => $this->tistatustime, 
			'tiexecutantread' => $this->tiexecutantread,

			'tiincidenttime'  => $this->tiincidenttime,
			'tiopenedtime'	  => $this->tiopenedtime,
			'tiplannedtime'	  => $this->tiplannedtime,
			'tiplannedtimenew'=> $this->tiplannedtimenew,
			'tiiplannedtime'  => $this->tiiplannedtime,
			'tisplannedtime'  => $this->tisplannedtime,
			'ticlosedtime'    => $this->ticlosedtime,

			'tiobject_id'     => $this->tiobject_id,
			'tiproblemtype_id'=> $this->tiproblemtype_id,
			'tiproblemtext'   => $this->tiproblemtext,
			'tidescription'   => $this->tidescription,

            'tifacility_id'   => $this->tifacility_id,
			'tifacilitycode'  => $this->tifacilitycode,
			'tiobjectcode'    => $this->tiobjectcode,
            'tiequipment_id'  => $this->tiequipment_id,
			'tiregion'        => $this->tiregion,
			'tiaddress'       => $this->tiaddress,

			'tioriginator'    => $this->tioriginator,
			'tioriginatordesk_id' => $this->tioriginatordesk_id,
			'tidesk_id'	      => $this->tidesk_id,

			'ticaller'        => $this->ticaller,
			'ticallerphone'   => $this->ticallerphone,
			'ticalleraddress' => $this->ticalleraddress,
			'ticalltype'      => $this->ticalltype,

			'tiresumedtime'     => NULL,
			'tiresulterrorcode' => NULL,
			'tiresulterrortext' => NULL,

			'tidivision_id'     => $this->tidivision_id,
			'tiexecutant_id'    => $this->tiexecutant_id,
			])->execute();    
            $this->recid = Yii::$app->db->getLastInsertID();;

		$res = $this->recid;
		return $res;
    }

    public function MakeLogRecord()
    {
		Yii::$app->db->createCommand()->insert('ticketlog',[
			'tiltime'       	=> $this->tiopenedtime,
			'tilplannedtime'	=> $this->tiplannedtime,
			'tiltype'       	=> 'WORKORDER',
			//'tiltext'			=> $this->,
			'tilstatus'     	=> $this->tistatus, 
			//'tilerrorcode'		=> $this->,
			'tilticket_id'  	=> $this->recid,
			'tilsender_id'		=> $this->tioriginator_id,
			'tilsenderdesk_id'	=> $this->tioriginatordesk_id,
			'tilreceiver_id'	=> $this->tiexecutant_id,
			'tilreceiverdesk_id'=> $this->tidesk_id,
			])->execute();    	
    }

    public function ExportLog($tiid)
    {
        TicketAction::exportIteraLog($tiid, $this->tistatus, $this->tioriginator_id, $this->tiexecutant_id, NULL, true);
    }

	public static function getGetTicketId()
	{
        $command = Yii::$app->db->createCommand("call elevators.getNewTicketRegNumber(@regnum, @regnumstr)")->execute();
        return  Yii::$app->db->createCommand("select @regnumstr as acnstr;")->queryScalar();
	}

    public static function getFacilityCod($FacilityID = NULL)
    {
    	$res = '-';
    	if (!is_null($FacilityID)) {
        	$res = Yii::$app->db->createCommand('SELECT id,facodesvc FROM  facility where id=:fid')->bindValues([':fid'=>$FacilityID])->queryOne()['facodesvc'];
    	}
        return $res;	
    }

    public static function getRegionName($RegionId = NULL)
    {
    	$res = '-';
    	if (!is_null($RegionId)) {
        	$res = Yii::$app->db->createCommand('SELECT districtname, districtcode FROM  district where districtcode=:rid')->bindValues([':rid'=>$RegionId])->queryOne()['districtname'];
    	}
        return $res;	
    }

	public function isUserMaster()
    {
    	$useroprights = Tickets::getUserOpRights();
        if( $useroprights ) return ((FALSE === strpos($useroprights['oprights'],'M') )&&(FALSE === strpos($useroprights['oprights'],'m'))) ? FALSE : TRUE;
    	return FALSE;
    }    

    public static function getOriginatorName()
    {
        $Originator = Yii::$app->db 	// may be FALSE, if user have not a corresponding record in employee table
        	->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as name, division_id FROM  employee where user_id=:uid')->bindValues([':uid'=>Yii::$app->user->id])
        	->queryOne()['name'];
    	$this->tioriginator_id = $Originator['id'];
        return $Originator['name'];
    }

    public static function getAdressStr( $aStreet ,$aFacility ,$aObject ,$aElevator, $aEntrance, $aApartment)
    {
    	$Street = Yii::$app->db->createCommand('SELECT id, streetname,streettype FROM street WHERE id = :sid ;')->bindValues([':sid' => $aStreet])->queryOne();
    	$Facility = Yii::$app->db->createCommand('SELECT id, faaddressno FROM facility WHERE id = :fid ;')->bindValues([':fid' => $aFacility])->queryOne();
    	if (!empty($aEntrance)) $Object = ', п.'.$aEntrance; 
   		else $Object = ''; 
    	if ('001' == $aObject){
			$Object = $Object.", ".Yii::$app->db->createCommand('SELECT id, concat(" ",ifnull(elporchpos,"")," ", ifnull(eltype,"")) as text FROM elevator WHERE id = :eid;')->bindValues([':eid' => $aElevator])->queryOne()['text'];
    	}else{
	    	if (!empty($aApartment)) { $Object = $Object .', кв.'.$aApartment; }
    	}
    	$addr = $Street['streettype'].' '.$Street['streetname'].', буд.'.$Facility['faaddressno'].$Object;
    	return $addr;
    }

    public function fillOriginator($UserID)
    {
        $originator = Yii::$app->db 	// may be FALSE, if user have not a corresponding record in employee table
        	->createCommand('SELECT id, concat( ifnull(lastname,"")," ",ifnull(firstname,"")," ",ifnull(patronymic,"")) as name, division_id FROM  employee where user_id=:uid')->bindValues([':uid' => $UserID])
        	->queryOne();
        $this->tioriginator_id = $originator['id'];
        $this->tioriginator = $originator['name'];
        $this->tioriginatordesk_id = $originator['division_id'];
        return $this->tioriginator;
    }

    public function fillElevatorDivision($ElevatorID)
    {
    	$eldivigion = Yii::$app->db->createCommand('SELECT id, eldivision_id, elperson_id, elinventoryno FROM  elevator where id=:eid')->bindValues([':eid' => $ElevatorID])->queryOne();
		$this->tidivision_id = $eldivigion["eldivision_id"];  
		//$this->tiexecutant_id = $eldivigion["elperson_id"];  
		$this->tiobjectcode =  $eldivigion["elinventoryno"];  
    }

    public function getTicketInfo($tiID)
    {
    	$this->recid = $tiID;
    	$ticrec = Yii::$app->db->createCommand('SELECT id, ticode, tiopenedtime, tioriginator FROM  ticket where id=:tid')->bindValues([':tid' => $tiID])->queryOne();
    	if (!empty($ticrec)){
    		$this->ticode = $ticrec['ticode'];
    		$this->tiopenedtime = $ticrec['tiopenedtime'];
    	}
    }

    public static function isWorkTime()
    {
    	$res = false;
    	$hours = getdate()['hours'];
    	if ( ($hours>=8) && ($hours<=17) ) { $res = true; }
    	return $res;
    }

    public  static function getDivisionMasterId($DivId)
    {
    	$master = Yii::$app->db->createCommand('SELECT e.id FROM employee e join division d ON e.division_id=d.id WHERE e.oprights LIKE "%M%" AND d.id=:did;')->bindValues([':did' => $DivId])->queryOne();    	
    	return $master['id'];
    }

    public static function getTickPlanTimeSpan($PrblmID = 0)
    {
        $res = 259200;
        $ticrec = Yii::$app->db->createCommand('SELECT tiproblemrepairterm, tiproblemtypecode FROM  ticketproblemtype WHERE id=:PrblmID')->bindValues([':PrblmID' => $PrblmID])->queryOne();
        if (!empty($ticrec)){
            if (!empty($ticrec['tiproblemrepairterm'])){
                $res = $ticrec['tiproblemrepairterm'];
            }
        }
        return $res;
    }

}
