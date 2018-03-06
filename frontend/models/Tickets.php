<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;


/*
 * This is example code for how to get data from db:
 *	1. With createComman: see dgetTicketsList()
 *	2. With SqlDataProvider: see search()
 */
class Tickets extends Model
{
	const LASDIVISION_CODE = 8;
	const OPERATORDIVISION_CODE = 6;

	public $ticket;			// the 1-dimention array  with current record in the ticket table
	public $tilogprovider;	// array with all records from ticketlog
	public $tispartprovider;// array with all records from ticketlog for spare parts
	public $fitterslist;	// the 2-dimention array  with records from the employee table
	public $useroprights;	// the 1-dimention array  ['id','division_id','oprights',] with currently logged in user rights
	public $elerrorcodelist;// the 2-dimention array  with elevator error codes
	public $spartlist;		// the 2-dimention array  with elevator error codes
	public $uploadedfilelist;	// array of file names in uploads directory for the ticket
	
	public $hasOos;			// if TRUE, the elevator is in or was in an Out-Of-Service conditions
	public $hasOosNow;		// if TRUE, the elevator is in an Out-Of-Service conditions NOW
	public $oosHours;		// the number of hours, during wich elevator has been or is in an Out-Of-Service conditions
	
	public $tilist;
	public $PartsClassList;
	public $actor;
	//---Filter for tickets list in index
	public $fltrDistrict;	// Filter: district for filtering

	/*---171020,did start---*/
    public static function GetPartsList($classid=0)
    {
    	if ($classid == 0) { 
            $PartsList = Yii::$app->db->createCommand('SELECT id,elspname as text, elspunit
            	                                       FROM elevatorsparepart 
        	                                           WHERE NOT (elspcode LIKE "%.0.0")
            	                                       ')->queryAll();	
    	}else{
            $PartsList = Yii::$app->db->createCommand('SELECT id,elspname as text, elspunit
            	                                       FROM elevatorsparepart 
        		                                       where (elspcode LIKE "'.$classid.'.%.%") 
        	                                             AND NOT (elspcode LIKE "'.$classid.'.0.0")
        	                                           ')->queryAll();	
    	}
    	return $PartsList;
    }
    public static function GetPartUnit($elspid=0)
    {
    	$select= Yii::$app->db->createCommand('SELECT id, elspunit FROM elevatorsparepart WHERE id ='.$elspid.' ; ' )->queryOne();	
    	if (isset($select['elspunit'])) return $select['elspunit'];
    	else return 'шт';

    }
    /*---171020,did end---*/

	public static function getCallTypesList(){
    	$calltypes = ArrayHelper::map(Yii::$app->db->createCommand('SELECT DISTINCT ticalltype FROM ticket order by ticalltype')->queryAll(),'ticalltype','ticalltype');
    	return $calltypes = [""=>'Все']+$calltypes;
    }
	public static function getDistrictsList(){
    	$districts = ArrayHelper::map(Yii::$app->db->createCommand('SELECT id,districtname FROM district where districtlocality_id=159')->queryAll(),'districtname','districtname');
    	return $districts = [""=>'Все']+$districts;
    }
	public function getTicketsList()
	{
		$tilist = Yii::$app->db->createCommand('SELECT * FROM ticket')->queryAll();
		//Yii::warning($tilist,__METHOD__);
		return $tilist;
	}	
	public static function getMonthsList($all=false) {
		return ((FALSE===$all)?[]:[0=>'Все']) + [1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',11=>'11',12=>'12'];
	}
	public static function getYearsList($all=false) {
		$years = array();
		$yearfrom = Yii::$app->db->createCommand("SELECT MIN(YEAR(tiopenedtime)) as y FROM ticket;" )->queryOne()['y'];	
		$yearto = date('Y');
		if(empty($yearfrom))$yearfrom=$yearto;
		for($i=$yearfrom;$i<=$yearto;$i++)$years[$i]=$i;
		return ( (FALSE===$all) ? [] : [0=>'Все'] ) + $years;
	}
    /**
	 * Gets all records from ticket db table, gets rights for currenly logged in user
	 * @param boolean $ticketsFilterAll - filtering condition
	 */
	public function search($ticketsFilterAll)
	{
		$tiplannedtime = $this->isUserMaster() ? 'tiplannedtimenew':'tiiplannedtime';
		$udeskid = $this->useroprights['division_id'];	// DeskId for currently logged in user
		//$sqltext='SELECT ticket.id as id, tipriority, ticode, tistatus, tiplannedtime,tiaddress, CONCAT(lastname," ", firstname) as executant FROM ticket left join employee on employee.id=tiexecutant_id';
		$sqltext='SELECT ticket.*, CONCAT(lastname," ", firstname) as executant FROM ticket left join employee on employee.id=tiexecutant_id';
		
		//---Prepare the sql statement for tickets according to the user rights
		if( $this->isUserMaster() ) {
			$sqltext = $sqltext." where (($udeskid in (tidesk_id,tidivision_id)) and (tidesk_id!=".self::OPERATORDIVISION_CODE."))";
			if( !$ticketsFilterAll) $sqltext = $sqltext.' and tistatus not in ("MASTER_COMPLETE", "DISPATCHER_COMPLETE", "OPERATOR_COMPLETE", "1562_COMPLETE", "KAO_COMPLETE", "MASTER_REFUSE")';
		} 
		else if( $this->isUserDispatcher() || $this->isUserOperator()) { 
			//---V.0: Filter for all cards where current user desk is originator or executor one
			//$sqltext = $sqltext." where ($udeskid in (tioriginatordesk_id,tidesk_id))";
			//---V.1: Filter for all cards where current user desk is DISPATCHER; OR is OPERATOR and is originator or executor one
			$sqltext = $sqltext." where ticode like '%'";
			if($this->isUserOperator())$sqltext = $sqltext.' and (tioriginatordesk_id='.$this->useroprights['division_id'].' OR tidesk_id='.$this->useroprights['division_id'].')';
			//---V.2: Filter for all cards where current user desk is originator or executor or recipient in ticket history one
			//$sqltext = $sqltext." where (($udeskid in (tioriginatordesk_id,tidesk_id)) OR $udeskid in (select tilreceiverdesk_id from ticketlog where tilticket_id=ticket.id)  )";
			//---Filter for {all} | {not completed}
			if( !$ticketsFilterAll) $sqltext = $sqltext.' and tistatus not in ("DISPATCHER_COMPLETE","OPERATOR_COMPLETE","1562_COMPLETE","KAO_COMPLETE")';
			//---Filter for district
			if($this->isUserDispatcher() && (!empty($this->fltrDistrict)))	$sqltext = $sqltext." and tiregion like \"$this->fltrDistrict\"";
		} 

		else if( $this->isUserFitter() ) {
			$sqltext = $sqltext.' where tiexecutant_id='.$this->useroprights['id']. " and tistatus not like '%COMPLETE%' and tistatus not like '%REFUSE%'";
		}
		else 	// Guest
			if( !$ticketsFilterAll)$sqltext = $sqltext." where  tistatus not in ('MASTER_COMPLETE', 'DISPATCHER_COMPLETE', 'OPERATOR_COMPLETE', '1562_COMPLETE', 'KAO_COMPLETE')";
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tipriority',
					'ticode',
					'tistatus',
					'tistatustime',
					$tiplannedtime,
					'tiaddress',
					'executant'
				],
				'defaultOrder' => [ 'ticode' => SORT_DESC ],
			],
		]);
		return $provider;
	}
	/**
	 * Gets an only separate record from ticket table
	 * @param integer $id - key for current record from ticket table
	 * @return The Tickets model instance with filled members for ticket itself, user who logged in, and fitters of the user's department
     */
	public function findOne($id)
	{
		//---Get list of classes of parts // получить классификацию ремкомплекта, 171020,did
        $this->PartsClassList = Yii::$app->db->createCommand('SELECT id,elspcode,elspname FROM elevatorsparepart WHERE elspcode LIKE "%.0.0" ')->queryAll();	
		//---Get known who is current user and take all fitters from his department
		$this->useroprights = $this->getUserOpRights();
		$this->actor = $this->getActor();
		$this->fitterslist = $this->getFittersList($this->useroprights['division_id']);

		//---Get records from ticket for given ticket
		if(!isset($this->ticket)){
			$sql4ticket=
			'SELECT ticket.*, tiobject, tiproblemtypetext,d.divisionname as divisionname,d1.divisionname as deskname,d2.divisionname as originatordeskname,d3.divisionname as executantdeskname,CONCAT(lastname," ",firstname," ",patronymic) as executant, oostypetext from ticket 
				left join ticketobject on ticketobject.id=tiobject_id 
				left join ticketproblemtype on ticketproblemtype.id=tiproblemtype_id 
    			left join employee on employee.id=tiexecutant_id
    			left join division d on d.id=tidivision_id 
    			left join division d1 on d1.id=tidesk_id 
    			left join division d2 on d2.id=tioriginatordesk_id 
    			left join division d3 on d3.id=employee.division_id 
    			left join oostype on tioostype_id=oostype.id where ticket.id='.$id; 

    		$this->ticket = Yii::$app->db->createCommand($sql4ticket)->queryOne();
    		//--- Calculate OOS parameters
    		$this->hasOos = !empty($this->ticket['tioosbegin']);
			$this->hasOosNow = ( $this->hasOos && empty($this->ticket['tioosend']));
			if($this->hasOosNow)$this->oosHours = intval((time() - strtotime($this->ticket['tioosbegin'])) / 3600);
			else if($this->hasOos) $this->oosHours = intval((strtotime($this->ticket['tioosend']) - strtotime($this->ticket['tioosbegin'])) / 3600);
    	}

		//---Get all records from ticket log
		if(!isset($this->tilogprovider)){
			$sql4tilog = 
			'SELECT ticketlog.*, CONCAT(e1.lastname," ",e1.firstname) as sender,d1.divisionname as senderdesk, CONCAT(e2.lastname," ",e2.firstname) as receiver,d2.divisionname as receiverdesk  FROM ticketlog 
					left join employee e1 on e1.id=tilsender_id 
					left join employee e2 on e2.id=tilreceiver_id
					left join division d1 on d1.id=tilsenderdesk_id
					left join division d2 on d2.id=tilreceiverdesk_id where (tiltype="WORKORDER" or tiltype="SVCORDER") and tilticket_id='.$id;
			$this->tilogprovider = new SqlDataProvider([
				'sql' => $sql4tilog,
				'key' => 'id',
				'sort' => [
					'attributes' => [
						'tiltime',
					],
					'defaultOrder' => [ 'tiltime' => SORT_DESC ],
				],
			]);
		}
		//---Get all from spare part records
		if(!isset($this->tispartprovider)){
			$sql4tispart = 
			'SELECT ticketlog.*, CONCAT(e1.lastname," ",e1.firstname) as sender,d1.divisionname as senderdesk, CONCAT(e2.lastname," ",e2.firstname) as receiver,d2.divisionname as receiverdesk  FROM ticketlog 
					left join employee e1 on e1.id=tilsender_id 
					left join employee e2 on e2.id=tilreceiver_id
					left join division d1 on d1.id=tilsenderdesk_id
					left join division d2 on d2.id=tilreceiverdesk_id where tiltype="SPORDER" and tilticket_id='.$id;
			$this->tispartprovider = new SqlDataProvider([
				'sql' => $sql4tispart,
				'key' => 'id',
				'sort' => [
					'attributes' => [
						'tiltime',
					],
					'defaultOrder' => [ 'tiltime' => SORT_DESC ],
				],
			]);
			//$this->ticketlog = Yii::$app->db->createCommand($sql4tilog)->queryAll();
		}
		//---Get spare part catalog
		if(!isset($this->spartlist)){
			$this->spartlist = Yii::$app->db->createCommand('SELECT id,CONCAT(IFNULL(elspcode,"")," ",elspname) as elspart,elspunit FROM elevatorsparepart')->queryAll();	
		}
		//---Get error codes for dropdown list
		if(!isset($this->elerrorcodelist)){
			$this->elerrorcodelist = Yii::$app->db->createCommand('SELECT elerrorcode as errorcode,CONCAT(elerrorcode," ",elerrortext) as errortext FROM elevatorerrorcode')->queryAll();
		}
		//---Get all uploaded files for the ticket
		if(!isset($uploadedfilelist)){
			$this->uploadedfilelist=UploadImage::getUploadedFileList($this->ticket['ticode'].'*');
		}
		return  $this;
	}
	/**
     *  Gets the info on rights, id and division for currently logged in user
     * @return mixed, array ['id','division_id','oprights'] if user is logged in and have a rights for some operations, boolean FALSE otherwise
     *
     */
    public static function getUserOpRights()
    {
        if(Yii::$app->user->isGuest) return FALSE;	// user is not currently logged in

        return Yii::$app->db 	// may be FALSE, if user have not a corresponding record in employee table
        	->createCommand('SELECT id,division_id,oprights from employee where user_id=:uid')->bindValues([':uid'=>Yii::$app->user->id])
        	->queryOne();
    }
	/**
	 * Gets string with currently logged in user main role
	 * @return mixed string or boolean FALSE
     */
	public function getActor()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) {
    		if(	     FALSE !== strpos( $this->useroprights['oprights'],"M" ) ) return 'MASTER';
    		else if( FALSE !== strpos( $this->useroprights['oprights'],"F" ) ) return 'EXECUTANT';
    		else if( FALSE !== strpos( $this->useroprights['oprights'],"D" ) ) return 'DISPATCHER';
    		else if( FALSE !== strpos( $this->useroprights['oprights'],"d" ) ) return 'DISPATCHER';
    	}
    	return FALSE;
    }/**
	 * Tests if the currently logged in user have a Dispatcher (CDS) rights
	 * @return boolean result
     */
	public function isUserDispatcher()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'D') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Tests if the currently logged in user have a Operator ( dispatcher ODS) rights
	 * @return boolean result
     */
	public function isUserOperator()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'d') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Tests if the currently logged in user have a Master rights
	 * @return boolean result
     */
	public function isUserMaster()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'M') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Tests if the currently logged in user have a foreman rights
	 * @return boolean result
     */
	public function isUserFitter()
    {
    	if( !isset($this->useroprights) ) $this->useroprights = $this->getUserOpRights();
    	if( $this->useroprights ) return (FALSE === strpos($this->useroprights['oprights'],'F') ) ? FALSE : TRUE;
    	return FALSE;
    }
    /**
	 * Builds the array of employees [[0]=>['id','name'],...] for given division who are the fitters ( occupation_id = 3 )
	 * @param integer $divisionId - key for the record in division table
	 * @return mixed, string if user is logged in and have a rights for some operations, FALSE otherwise
     */
	protected function getFittersList($divisionId)
	{
		if($this->isUserMaster()){
		//----Get all fitters for division
		$fitters = Yii::$app->db
			->createCommand('SELECT id,concat(lastname," ",firstname," ",patronymic) as name FROM employee where oprights like "F" and division_id=:id order by name')
			->bindValues([':id' => $divisionId])
			->queryAll();
		}
		else if ( $this->isUserDispatcher() ) {
		//---Get all emergency workers
		$fitters = Yii::$app->db
			->createCommand('SELECT e.id,concat("ЛАС: ",lastname," ",firstname," ",patronymic) as name FROM employee e join division d on e.division_id=d.id where oprights like "F" and divisioncode=:id order by name')
			->bindValues([':id' => self::LASDIVISION_CODE])
			->queryAll();
		}
		else return [];

		return ArrayHelper::map($fitters,'id','name');
	}
	/**
	 * Prepares the array of pairs [division.id,divisionname] for filling in http select - for divisions where Masters are employed
	 * @return array of [id,divisionname]
     */
	public static function getMasterDesksList(){
		$masterDesks = Yii::$app->db
			->createCommand("select distinct d.id, d.divisionname from division d left join employee e on e.division_id = d.id where e.oprights like '%M%';")->queryAll();
		return ArrayHelper::map($masterDesks,'id','divisionname');
	}
	/**
	 * Prepares the array of pairs [division.id,divisionname] for filling in http select - for divisions where Masters are employed
	 * @return array of [id,oostypetext]
     */
	public static function getOosTypesList(){
		$oosTypes = Yii::$app->db
			->createCommand("select id,oostypetext from oostype;")->queryAll();
		return ['0'=>'Причина не определена!'] + ArrayHelper::map($oosTypes,'id','oostypetext');
	}
	public static function getHoursList(){
		return ['00'=>'00:00','01'=>'01:00','02'=>'02:00','03'=>'03:00','04'=>'04:00','05'=>'05:00','06'=>'06:00','07'=>'07:00','08'=>'08:00','09'=>'09:00','10'=>'10:00','11'=>'11:00','12'=>'12:00','13'=>'13:00','14'=>'14:00','15'=>'15:00','16'=>'16:00','17'=>'17:00','18'=>'18:00','19'=>'19:00','20'=>'20:00','21'=>'21:00','22'=>'22:00','23'=>'23:00','24'=>'24:00',];
	}
	
	/**
	 * Sets the tilreadflag in ticket log for record with newest time !FOR LOGGED IN USER!
	 * @param integer $id - ticket id
     */
	public static function setReadFlag($id){
		if( FALSE === ( $receiver = Tickets::getUserOpRights() ) ) return;
		//--Here 1 version - set readflag in ticketlog
		$result = Yii::$app->db->createCommand('SELECT tiltime, id FROM ticketlog WHERE tilticket_id = '.$id.' AND tilreceiver_id = '.$receiver['id'].' ORDER BY tiltime DESC LIMIT 1' )->queryOne();		
		Yii::$app->db->createCommand()->update('ticketlog',['tilreadflag'=>'1'],['id'=>$result['id']])->execute();
		//--Here 2 version - set readflag in ticket
		Yii::$app->db->createCommand()->update('ticket',['tiexecutantread'=>'1'],['id'=>$id,'tiexecutant_id'=>$receiver['id']] )->execute();
	}
	/**
	 * Sets the tilreadflag in ticket log for record with newest time !FOR LOGGED IN USER!
	 * @param integer $id - ticket id
	 * @param integer $receiver -  id of person to whom message been sent
     */
	public static function isTicketBeenRead( $id, $receiver ){
		if(!isset($receiver))return FALSE;
		$result = Yii::$app->db->createCommand('SELECT tiltime, tilreadflag FROM ticketlog WHERE tilticket_id = '.$id.' AND tilreceiver_id = '.$receiver.' ORDER BY tiltime DESC LIMIT 1' )->queryOne();		
		//Yii::warning('READ==='.$result,__METHOD__);
		return $result['tilreadflag'] ? TRUE : FALSE;
	}
	
}