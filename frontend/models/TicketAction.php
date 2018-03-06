<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;


/*
 * This is example code for how to get data from db:
 *	1. With createComman: see dgetTicketsList()
 *	2. With SqlDataProvider: see search()
 */
class TicketAction extends Model
{
	//---GET fields:
	public $tistatus;		// From GET:operation description
	//---POST form hidden fields:
	public $ticketId;		// From POST: ticket id
	public $senderId;		// From POST: currently logged in user id
	public $senderdeskId;	// From POST: currently logged in user department id
	public $servicedeskId;	// From POST: service  department id
	public $actor;			// From POST: MASTER or DISPATCHER or EXECUTANT
	//---POST form fields:
	public $tiltext;		// From POST: operation description
	public $tiplannedtimenew;// From POST: planned time for ticket
	public $tiiplannedtime;	// From POST: planned time for executant
	public $errorcode;		// From POST: error code set by executant in his report
	public $tidesk_id;		// From POST: responsible division, assigned by actor to handle the ticket (should be saved in ticket.tidesk_id)
	public $tioosbegin;		// OOS beginning date
	public $tioosbegintm;	// OOS beginning time
	public $tioosend;		// OOS ending date
	public $tioosendtm;		// OOS ending time
    public $tioostype_id;	// OOS type ID

	//---POST form fields or calculated
	public $receiverId;		// got it from post only for case the foreman is current user, otherwise should get it from db

	const LASDIVISION_ID = 8;

	/**
	 * Itera-machine
	 * 1.Fills all actions as records to the exportlog table for further export its to external system by external  transmitter task.
	 * 2.Tries to immediately send messages to Itera
	 * @param int ticket_id - ticket.id, should be valid id of ticket , otherwise function returns FALSE
	 * @param string opstatus - the operation status string (like EXECUTANT_COMPLETE etc)
	 * @param int person_id - should be valid employee.id, otherwise function returns FALSE
	 * @param string comment - the string with comment to operation
	 * @param boolean isnew - should be set to true if ticket is newly created one
	 * @return mixed - FALSE on error, or int value, the exportiteralog.id - id of record inserted into exportiteralog
	 */
	public static function exportLog($ticket_id, $opstatus, $person_id,$comment,$isnew=false){
		$CUDBG=false;	// set to debug curl
		//--- To let writing to DB-Log without sending to Itera: 1.Set exportItera=No (frontend/config/params-local.php). 2.Do comment the string below
		//if( FALSE===strpos(Yii::$app->params['exportItera'],'Yes' )) return; 
		$curloptions = [
			//CURLOPT_USERPWD=>'hglapi:hglapi',	// To remember login:password
			//CURLOPT_SSL_VERIFYPEER=>false,	// For future, if will migrate to https
			//CURLOPT_SSL_VERIFYHOST=>0,		// For future, if will migrate to https
			CURLOPT_HTTPGET=>TRUE,		// Set to reset http method to initial state (GET)-in case when performing >1 request not closing CURL handle
			CURLOPT_HEADER=>0,			// Do not return Headers in the result
			CURLOPT_RETURNTRANSFER=>TRUE,
			CURLOPT_TIMEOUT=>4,
			CURLOPT_COOKIEFILE=>"",		// Set to an empty string to enable Cookie engine!!!
			//CURLOPT_COOKIEJAR=>"CookieIt.txt",	// To save Cookie in file, for debugging
			//CURLOPT_COOKIE => '.ASPXAUTH=31B9E864F0C0808EC227A6A73AF51BDF1E6B59A884ADF6A5114E702419B9CA9D2BBD8983579FB4A432B0E2C590EB7F83EC1126A5DFE150624A7C916E29F3980B626CCBA685BE5BD2E49A745A06BEEB1D2B99391B1C0E5FA26E1AB08096BE8734DB949D03E82A18F8C406AD2E0CDE0AD8E3D8E12C46A5AE1E81B59C74977E4CEF918241C07ED665B1364AA6F68A72300BCB3AC07C19531BA0D4BE8D112AEDD7B4F205C331BB32AC9D22A6CA7DECA8DA21504D09D882C89A21384D6230E356CC02686FFD5956F3CEFBEE56EF06568CB72D22C827FBBA2471215014A35AD80CDFE7BB977630AF9083750973D7D6768969EC56A6FED85DAD64A217C4D32B57C710D6ACD031BB195836FFC0EC7DF3F1210FBDF3A9F1D928BE96266179F10C38E228340841EC6EFCA050F50BC161FA309695D6D03FD24A01D9E53C1E69E9CE00C7595DA82744AEC133EE6983106C31F1B1759D',
		];
		//--- Get original ticket
		if(!$ticket_id)			return FALSE;
		if( FALSE==($dbticket=Yii::$app->db->createCommand("SELECT * FROM ticket left join oostype ON ticket.tioostype_id=oostype.id left join ticketproblemtype  ON ticket.tiproblemtype_id=ticketproblemtype.id where ticket.id=$ticket_id")->queryOne())) 	return FALSE;
		if( FALSE == ($dbemployee = Yii::$app->db->createCommand("SELECT * FROM employee where id=$person_id")->queryOne())) return FALSE;

		//--- Set original ticket fields:
		$recordtime = date("Y-m-d H:i:s");
		$ticode1562 = $dbticket['ticoderemote']; // 1562 number
		$isnew=$isnew?1:null;
		//--- Set fields for external system (Itera)
		$iterastates=[
			1=>"НОВАЯ",2=>"ОТКЛОНЕНА",3=>"ОТКЛОНЕНА (Подтв.)",5=>"РАСПРЕДЕЛЕНА",9=>"В РАБОТЕ",10=>"ОЖИДАЕТ ЗЧ",11=>"ЗАКРЫТА",12=>"ЗАКРЫТА (Подтв.)",13=>"РАБОТА ОКОНЧЕНА"
		];
		//--- Set rdevice_id
		$tiobjectcode=$dbticket['tiobjectcode'];
		if(!empty($tiobjectcode))
			$rdevice_id = Yii::$app->db->createCommand("SELECT * FROM elevator where elinventoryno like '$tiobjectcode'")->queryOne()['elremoteid'];
		//--- Set malfunction_id
		$rmalfunction_id = $dbticket['oostypecode'];
		if(empty($rmalfunction_id))$rmalfunction_id = 3; /* if not set yet, by default - Аварийное событие */
		//--- Set rstatus_id - the Itera status. Here we doesn't process the incoming (NEW) 1562 tickets, assuming Itera handles its by itself
		switch($opstatus){
			case 'OPERATOR_ASSIGN':		$rstatus_id = 1;	break; /* НОВАЯ    */

			case 'EXECUTANT_ACCEPT':
			case 'MASTER_ACCEPT':
			case 'DISPATCHER_ACCEPT':	$rstatus_id = 9;	break; /* В РАБОТЕ */

			case 'OPERATOR_COMPLETE':
			case 'DISPATCHER_COMPLETE':	$rstatus_id = 11;	break; /* ЗАКРЫТА */

			case 'DISPATCHER_ASSIGN_MASTER':	
			case 'DISPATCHER_ASSIGN':
			case 'DISPATCHER_REASSIGN':
			case 'MASTER_ASSIGN':		$rstatus_id = 5;	break; /* РАСПРЕДЕЛЕНА */

			case 'DISPATCHER_REFUSE':	$rstatus_id = 2;	break; /* ОТКЛОНЕНА */
			
			case 'MASTER_COMPLETE':				
			case 'EXECUTANT_COMPLETE':	$rstatus_id = 13;	break; /* РАБОТА ОКОНЧЕНА */

			default:
			case 'EXECUTANT_REFUSE':	/*unset($rstatus_id);*/	$rstatus_id=9; break;/* В РАБОТЕ */
		}
		//--- Set rpriority_id - priority:
		switch($dbticket['tipriority']){
			default:
			case 'NORMAL': 		$rpriority_id = 3;	break;	/* ОБЫЧНЫЙ */
			case 'EMERGENCY': 	$rpriority_id = 1;	break;	/* Очень высокий */
			case 'CONTROL1': 	$rpriority_id = 5;	break;	/* К1 (письм.) */
			case 'CONTROL2': 	$rpriority_id = 6;	break;	/* К2 (письм.) */
		}
		//--- Set times
		$rcreated=$dbticket['tiopenedtime'];
		$rturnoff_time=$dbticket['tioosbegin'];
		$rturnon_plan_time=$dbticket['tiplannedtimenew'];
		$rturnon_time=$dbticket['tioosend'];

		//--- Set performer id
		$rperformer_id = $dbemployee['remoteid'];
		$division_id   = $dbemployee['division_id'];
		$isfitter      = (FALSE!==strpos($dbemployee['oprights'],'F'))?TRUE:FALSE;
		if( $isfitter ) {
			if($division_id==self::LASDIVISION_ID);	// should replace this on Master or Dispatcher Id, but after the understanding where to seek it
			else $rperformer_id = Yii::$app->db->createCommand("SELECT remoteid FROM employee where oprights like '%M%'and  division_id=$division_id")->queryOne()['remoteid'];
		}
		if(empty($rperformer_id))$rperformer_id = 89;	// Диспетчер 1 (ХГЛ)
		//--- Set description
		$rdescription=	"1.".$dbticket['tiproblemtypetext']."\n".
						"2.".$dbticket['tidescription']."\n".
						"3.".$dbticket['tiproblemtext']."\n".
						"4.".$dbticket['tiresulterrortext'].':'.$dbticket['tiresulterrorcode']."\n".
						"5.".$comment;
		//--- Looking for remote ticket number in a log table - hope, it was got it early by transmitter
		if(!$isnew) {
			$rticket_id = Yii::$app->db->createCommand("SELECT rticket_id from exportiteralog where (ticket_id=$ticket_id) and (rticket_id is not null)")->queryOne()['rticket_id'];
			if(!empty($rticket_id))Yii::$app->db->createCommand("UPDATE exportiteralog set rticket_id=$rticket_id where ticket_id=$ticket_id")->execute();
		}


//---(1) Login to Itera
if( FALSE!==strpos(Yii::$app->params['exportItera'],'Yes' )) {
		$txattempts=1;	// here we will do the one attempt to tx the record, if it fails, then external transmitter will do the further tryes
		$txcount=1;	// Will at least try to log in
		$iteraAPIurl = $txrequest = "http://bsmart.itera.ws/Account/Login";
		$ch=curl_init($iteraAPIurl);
		curl_setopt_array($ch,$curloptions+[
			CURLOPT_HTTPHEADER=>["Referer: ".$iteraAPIurl],
			CURLOPT_POST=>1,
			CURLOPT_POSTFIELDS=>['Login'=>Yii::$app->params['loginItera'],'Password'=>Yii::$app->params['passwordItera']],
		]);
		//-- For debugging
		if($CUDBG) { $fCurlOut = fopen('curl_out.txt', "w" );curl_setopt($ch,CURLOPT_VERBOSE,1);curl_setopt($ch, CURLOPT_STDERR, $fCurlOut); }
		if( FALSE === ( $txresult = curl_exec($ch) ) ) $txresult='Error:network failure when auth';
		else {
			$curlinfo=curl_getinfo($ch,CURLINFO_COOKIELIST);
			$curlinfostr = (is_array($curlinfo)) ? implode(' ',$curlinfo) : $curlinfo;
			$txresult = (FALSE!==strpos($curlinfostr,"ASPXAUTH")) ? 'Auth Ok!':'Error:failed to Auth';
			//-- For debugging
			if($CUDBG)fwrite($fCurlOut,"\n***CurlINFO:\n".$curlinfostr."\n***CurlINFOend\n");
		}
		$txresultAll.=str_replace("\n","",$txresult)."\n";
		//---(2) Try to ask Itera the remote ticket id, only if we have 1562 ticket and we yet not asked Itera
		//$ticode1562='7543002';	//---Debugging
		if( (!empty($ticode1562 )) AND empty($rticket_id)) { // it's 1562 ticket, trying to get its id from Itera or update remote info it we have it
				$iteraAPIurl = "http://bsmart.itera.ws/ds/Ticket?filter(no)=equals($ticode1562)";
				//curl_reset($ch);
				curl_setopt($ch, CURLOPT_URL, $iteraAPIurl);
				curl_setopt_array($ch,$curloptions);
				$txresult=curl_exec($ch);$txcount++;$txrequest.="\n".$iteraAPIurl;
				if(FALSE===$txresult)$txresult='Error:failed when asking 1562 number';
				else {
					//Yii::warning('======ITERA:'.$txresult,__METHOD__);
					$txresult=mb_convert_encoding($txresult,'UTF-8','UTF-8');
					$jsonAnswer=json_decode($txresult,true);
					if(is_array($jsonAnswer)) $rticket_id = $jsonAnswer['Records'][0]['id'];
				}
				$txresultAll.=mb_substr(str_replace("\n","",$txresult),0,50)."\n";
		}

		//---(3) Tx the ticket to Itera -> transmit all tickets exclude 1562, for which we failed to get the remote id
		if( !(empty($rticket_id) AND (!empty($ticode1562 ))) ) {	// Do this for all tickets, EXCLUDING  1562 tickets with EMPTY rticket_id (from ITERA)
			$iteraAPIurl = "http://bsmart.itera.ws/edt/Ticket/Post";	// New
			$postdata=[			
				'@device_id'=>$rdevice_id,
				'@malfunction_id'=>$rmalfunction_id,
				'@priority_id'=>$rpriority_id,
				'@performer_id'=>$rperformer_id,
				'@turnoff_time'=>$rturnoff_time,
				'@turnon_plan_time'=>$rturnon_plan_time,
				'@turnon_time'=>$rturnon_time,
				'@description'=>$rdescription,
				//'@created'=>$rcreated,
			];
			if(empty($ticode1562)) $postdata += ['@created'=>$rcreated];	// add creation time if it's not a 1562 ticket
			if(empty($rticket_id)){				// New
				//unset($postdata['@turnon_time']);
			}
			else{								// Edit existing
				$iteraAPIurl.="?id=$rticket_id";	
				//unset($postdata['@device_id']);
				//unset($postdata['@created']);
			}
			curl_setopt_array($ch,$curloptions+[
				CURLOPT_URL=>$iteraAPIurl,
				CURLOPT_POST=>1,
				CURLOPT_POSTFIELDS=>$postdata,
			]);
			$txresult=curl_exec($ch); 
			$txcount++; $txrequest.="\n".$iteraAPIurl;
			if(FALSE===$txresult)$txresult='Error:failed to transmit ticket';
			else {
					$txresult=mb_convert_encoding($txresult,"UTF-8");
					$jsonAnswer=json_decode($txresult,true);
					if(is_array($jsonAnswer)) 
						if( FALSE !== mb_strpos($jsonAnswer['Status'],"Ok")) {
							$irecUpdated=TRUE;
							if( empty($rticket_id) ) $rticket_id = $jsonAnswer['Result'];
						}
						else $irecUpdated=FALSE;
			}
			$txresultAll.=str_replace("\n","",$txresult)."\n";

			//---(4) Set new status for Itera ticket
			if($irecUpdated)if(!empty($rticket_id)){
				$iteraAPIurl = "http://bsmart.itera.ws/Ticket/SetStatus";	
				$postdata=[			
					'ticket_id'=>$rticket_id,
					'status_id'=>$rstatus_id,
				];
				curl_setopt_array($ch, $curloptions+[ 
					CURLOPT_URL=>$iteraAPIurl,
					CURLOPT_POST=>1,CURLOPT_POSTFIELDS=>$postdata
				]);
				$txresult=curl_exec($ch);$txcount++;$txrequest.="\n".$iteraAPIurl;
				if(FALSE===$txresult) $txresult='Error:failed to transmit status';
				else {
					$txresult=mb_convert_encoding($txresult,"UTF-8");
					$jsonAnswer=json_decode($txresult,true);
					if(is_array($jsonAnswer)) 
						if( FALSE !== mb_strpos($jsonAnswer['Status'],"Ok")) {
							$isexportdone=1;
					}
				}
				$txresultAll.=str_replace("\n","",$txresult)."\n";
			}
		}
//--- Loggoff from Itera
		curl_close( $ch );
		if($CUDBG)fclose($fCurlOut);
}
		//--- Try (again) to update remote status for all records of this ticket...
		if(!empty($rticket_id))Yii::$app->db->createCommand("UPDATE exportiteralog set rticket_id=$rticket_id where ticket_id=$ticket_id")->execute();
		//--- Write to exportLog
		Yii::$app->db->createCommand()->insert('exportiteralog',[
			//'recordtime'=>$recordtime,	// server will set time
			'ticket_id'=>$ticket_id,
			'tistatuslogged'=>$opstatus,
			'ticode1562'=>$ticode1562,
			'isnew'=>$isnew,
			'rstatus_id'=>$rstatus_id,
			'person_id'=>$person_id,
			'rperformer_id'=>$rperformer_id,
			'rdevice_id'=>$rdevice_id,
			'rmalfunction_id'=>$rmalfunction_id,
			'rpriority_id'=>$rpriority_id,
			'rcreated'=>$rcreated,
			'rturnoff_time'=>$rturnoff_time,
			'rturnon_plan_time'=>$rturnon_plan_time,
			'rturnon_time'=>$rturnon_time,
			'rdescription'=>$rdescription,
			'rticket_id'=>$rticket_id,
			
			//'txtime'=>$recordtime,	// server will set time
			'txattempts'=>$txattempts,
			'txcount'=>$txcount,
			'txrequest'=>mb_substr($txrequest,0,255),
			'txresult'=>mb_substr($txresultAll,0,255),
			'isexportdone'=>$isexportdone,
			])->execute();
		return intval(Yii::$app->db->getLastInsertID());
	}
	public function updateTiplanneddate()
	{
		$this->tiplannedtimenew = $this->tiplannedtimenew.'T17:00';
		Yii::$app->db->createCommand()->update('ticket',['tiplannedtimenew'=>$this->tiplannedtimenew],['id'=>$this->ticketId])->execute();
	}
	/**
	 * Out-Of-Service state machine
	 */
	public function updateOos()
	{	$opdate=date("Y-m-d H:i:s");
		//--- Get current OOS state from DB, prepare array for updating the log
		$dbticket=Yii::$app->db->createCommand("SELECT * from ticket where id=$this->ticketId")->queryOne();
		$array2log=[//'tiltime'       	=> date("Y-m-d H:i:s"), // server will set time
					'tiltype'       	=> 'WORKORDER',
					'tiltext'			=> $this->tiltext,
					'tilticket_id'  	=> $this->ticketId,
					'tilsender_id'		=> $this->senderId,
					'tilsenderdesk_id'	=> $this->senderdeskId ];

		//--- User have changed the OOS type
		if(strpos($this->tistatus,"ASSIGN")){
			$oostype = empty($this->tioostype_id) ? null:$this->tioostype_id;
			Yii::$app->db->createCommand()->update('ticket',['tioostype_id'=>$oostype],['id'=>$this->ticketId])->execute();
			$array2log += ['tilstatus'=> $this->actor.($oostype?'_OOSSETTYPE_HIDDEN':'_OOSRESETTYPE_HIDDEN')]; 
			Yii::$app->db->createCommand()->insert('ticketlog',$array2log)->execute();unset($oostype);
		}
		//--- User moves elevator to state of normal operations - from OOS state, or simply pressed a submit key without doing any changes 
		else if(strpos($this->tistatus,"SWITCH")){
			//--- User moves elevator from state of normal operations to  OOS state
			if( empty($dbticket['tioosbegin'] ) ){	
				$oosb = $dbticket['tiopenedtime'];
				$oose = null;
				Yii::$app->db->createCommand()->update('ticket',['tioosbegin'=>$oosb,'tioosend'=>$oose,],['id'=>$this->ticketId])->execute();
				$array2log += ['tilstatus'=> $this->actor.'_OOSBEGIN_HIDDEN']; 
				Yii::$app->db->createCommand()->insert('ticketlog',$array2log)->execute();
			} else {	
			//--- User moves elevator to state of normal operations - from OOS state, or simply pressed a submit key without doing any changes 
				Yii::$app->db->createCommand("UPDATE ticket set tioosbegin=null,tioosend=null where id=$this->ticketId")->execute();
				$array2log += ['tilstatus'=> $this->actor.'_OOSREFUSE_HIDDEN']; 
				Yii::$app->db->createCommand()->insert('ticketlog',$array2log)->execute();
			}
		}
		//--- User is editing the OOS dates, times, and type
		else if(strpos($this->tistatus,"EDIT")){
			$oosb = $oose = null;
			try{$oosb = Yii::$app->formatter->asDate($this->tioosbegin,'yyyy-MM-dd').' '.$this->tioosbegintm;}catch(\Exception $e){$oosb = null;}
			if( $oosb > $opdate) $oosb = null;
			if(!empty($this->tioosend))
			  try{$oose = Yii::$app->formatter->asDate($this->tioosend,'yyyy-MM-dd').' '.$this->tioosendtm;}catch(\Exception $e){$oose = null;}
			if( $oosb ) {
				if( $oose AND $oose <= $oosb ) $oose = null;
				Yii::$app->db->createCommand()->update('ticket',['tioosbegin'=>$oosb,'tioosend'=>$oose],['id'=>$this->ticketId])->execute();
				$array2log += ['tilstatus'=> $this->actor.'_OOSEDIT_HIDDEN']; 
				Yii::$app->db->createCommand()->insert('ticketlog',$array2log)->execute();
			}
		}
	}

	public function save()
	{
		$opdate = date("Y-m-d H:i:s");
		//---
		if( in_array($this->actor,['MASTER','DISPATCHER'] ) ) {
			$executant = $this->receiverId;
			//---Getting the department id of receiver by his id from request
			$result=Yii::$app->db->createCommand('SELECT division_id from employee where id=:empid')->bindValues([':empid'=>$this->receiverId])->queryOne();
			$receiverdeskId = $result['division_id'];
			//Yii::warning('======'.$servicedeskId,__METHOD__);
			$this->errorcode='-';
		}
		else if( $this->actor == 'EXECUTANT' ) { 
			// should find  the receiverId (who had placed the workorder) if executant is current user
			$result=Yii::$app->db->createCommand('SELECT tiltime, tilsender_id,tilsenderdesk_id from ticketlog where tiltype like "WORKORDER" and tilstatus like "MASTER_%ASSIGN" and tilreceiver_id=:empid ORDER BY tiltime LIMIT 1')->bindValues([':empid'=>$this->senderId])->queryOne();
			$this->receiverId = $result['tilsender_id']; 
			$receiverdeskId = $result['tilsenderdesk_id']; 
			$executant = $this->senderId;
			$errortext = Yii::$app->db->createCommand('SELECT elerrortext from elevatorerrorcode where elerrorcode=:errorcode')->bindValues([':errorcode'=>$this->errorcode])->queryOne()['elerrortext'];
		}
		//---
		$fields4update = [
			'tistatus'=>$this->tistatus,
			'tistatustime'=>$opdate,
			'tiexecutant_id'=>$executant,
		];
		if( $this->actor == 'MASTER' ){ $fields4update['tidesk_id'] = $this->senderdeskId;/*$this->servicedeskId;*/ }
		else if( $this->actor == 'DISPATCHER' ){ $fields4update['tidesk_id'] = $this->senderdeskId; }
		else if($this->actor == 'EXECUTANT'){
			$fields4update['tiresulterrorcode'] = $this->errorcode;
			$fields4update['tiresulterrortext'] = $errortext;
		}
		if( in_array($this->actor,['MASTER','DISPATCHER'] ) ) switch($this->tistatus){
				case 'DISPATCHER_ASSIGN':
				case 'MASTER_ASSIGN':   $markasunread = TRUE; $tilplannedtime=$this->tiiplannedtime.'T17:00'; break;
				case 'DISPATCHER_REASSIGN':
				case 'MASTER_REASSIGN': $markasunread = TRUE; $tilplannedtime=$this->tiiplannedtime.'T17:00'; break;
				
				case 'DISPATCHER_COMPLETE':unset($receiverdeskId);$this->receiverId = null;
				case 'MASTER_COMPLETE': 
						unset($fields4update['tiexecutant_id']);
						$fields4update['tiresumedtime'] = $fields4update['ticlosedtime'] = $opdate; break;

				case 'DISPATCHER_ASSIGN_MASTER': 
					//$fields4update['tistatus']  = 'DISPATCHER_ASSIGN';
					$fields4update['tidesk_id'] = $this->tidesk_id;//$this->servicedeskId;
					$receiverdeskId = $this->tidesk_id;
					$this->receiverId=Yii::$app->db->createCommand("SELECT id from employee where oprights like '%M%' and division_id=$receiverdeskId limit 1;")->queryOne()['id'];
					$executant =$this->receiverId;	// To send the SMS 
					$markasunread = FALSE; 
					$fields4update['tiexecutant_id']=null;
				break;
				case 'DISPATCHER_ACCEPT': 
				case 'MASTER_ACCEPT': 
				case 'DISPATCHER_REFUSE':
				case 'MASTER_REFUSE':
					$this->receiverId=null;
					$markasunread = FALSE; 
					$fields4update['tiexecutant_id']=null;
				break;
				case 'DISPATCHER_ASSIGN_DATE': 
				case 'MASTER_ASSIGN_DATE': 
					$receiverdeskId = Yii::$app->db->createCommand("SELECT id,tidesk_id from ticket where id=$this->ticketId ORDER BY id desc limit 1;")->queryOne()['tidesk_id'];
					$this->receiverId=null;
					unset($fields4update['tiexecutant_id']);
					unset($fields4update['tidesk_id']);
					$this->tiltext='Новый срок: '.$this->tiplannedtimenew.' 17:00';
					$tilplannedtime=$this->tiplannedtimenew.'T17:00';
					$fields4update['tiplannedtimenew'] = $tilplannedtime;
					
				break;
				case 'DISPATCHER_ASSIGN_OOS': 
				case 'DISPATCHER_EDIT_OOS': 
				case 'DISPATCHER_SWITCH_OOS': 
				case 'MASTER_ASSIGN_OOS': 
				case 'MASTER_EDIT_OOS': 
				case 'MASTER_SWITCH_OOS': 
					$this->updateOos();
					return;
				break;
		}
		if($this->tiiplannedtime)	{ $this->tiiplannedtime = $this->tiiplannedtime.'T17:00'; $fields4update['tiiplannedtime'] = $this->tiiplannedtime; }
		if( $markasunread )			$fields4update['tiexecutantread'] = null;	// mark as unread
		//Yii::warning($fields4update,__METHOD__);
		Yii::$app->db->createCommand()->update('ticket',$fields4update,['id'=>$this->ticketId])->execute();
		Yii::$app->db->createCommand()->insert('ticketlog',[
			//'tiltime'       	=> $opdate,	// server will set time
			'tilplannedtime'	=> $tilplannedtime,
			'tiltype'       	=> 'WORKORDER',
			'tiltext'			=> $this->tiltext,
			'tilstatus'     	=> $this->tistatus, 
			'tilerrorcode'		=> $this->errorcode,
			'tilticket_id'  	=> $this->ticketId,
			'tilsender_id'		=> $this->senderId,
			'tilsenderdesk_id'	=> $this->senderdeskId,
			'tilreceiver_id'	=> $this->receiverId,
			'tilreceiverdesk_id'=> $receiverdeskId
			])->execute();
		if( in_array( $this->tistatus,['MASTER_ASSIGN','MASTER_REASSIGN','DISPATCHER_ASSIGN','DISPATCHER_ASSIGN_MASTER','DISPATCHER_REASSIGN'] ) ) $this::sendSMS($executant, $this->senderId, $this->ticketId );
		self::exportLog($this->ticketId,$this->tistatus,$this->senderId,$this->tiltext,false);
		return;
	}
	/**
	 *	Sends SMS in http-get via tcp-gateway
	 *	195.3.196.246 - IP
	 *	4070  - port
	 *	u  - login
	 *	p  - password
	 *	l  - line number
	 *	n  - phone number
	 *	m  - message
	 */
	public static function sendSMS( $receiverId, $senderId, $ticketId ) { 
		//self::_sendSMS( $receiverId, $senderId, $ticketId ); 	// 18.02.2018 switched off, vpr
		self::sendEmail( $receiverId, $senderId, $ticketId );
	}
	public static function _sendSMS( $receiverId, $senderId, $ticketId ) {

		if( FALSE===strpos(Yii::$app->params['sendSMS'],'Yes'))return;
		//---Line number
		$lineNo = 2;			
		//194.106.218.106
		//---Prepare the phone number
		$phone = Yii::$app->db->createCommand('SELECT personphone from employee where id=:id')->bindValues([':id'=>$receiverId])->queryOne()['personphone'];
		$phone='8'.substr( $phone,-10,10 );	
		if( 11 != strlen( $phone ) ) return;
		if( ! preg_match( "/^(\d{11})/", $phone ) ) return;

		//---Prepare the SMS text
		$sendername = Yii::$app->db->createCommand('SELECT lastname from employee where id=:id')->bindValues([':id'=>$senderId])->queryOne()['lastname'];
		$mes='Заявка-'.$sendername.':%20'.rawurlencode("http://195.3.196.246/index.php?r=tickets%2Fview&id=$ticketId");

		//---Sending...
		$gatewayurl = "http://195.3.196.246:4070/default/en_US/send.html?u=admin&p=admin&l=$lineNo&n=$phone&m=$mes";
		$content = "";
		//if( FALSE != ( $fp = @fopen( $gatewayurl,"r" ) ) ) { while( !feof($fp) ) $content .= fread( $fp, 1024 ); fclose($fp); }	// 1 method
		$ch=curl_init($gatewayurl);curl_setopt_array($ch,[CURLOPT_HEADER=>0,CURLOPT_RETURNTRANSFER=>TRUE,CURLOPT_TIMEOUT=>4]);$content=curl_exec($ch); curl_close( $ch );
 		//print($gatewayurl);print($content);
 		Yii::$app->db->createCommand()->insert('smslog',['smsphone'=>$phone,'smsdir'=>'OUT','smscommand'=>$gatewayurl,'smsline'=>$lineNo,'smstext'=>$mes,'smslength'=>strlen($mes),'smsresult'=>$content])->execute();
	}
	/**
	 * Sends Email
	 */
	public static function sendEmail( $receiverId, $senderId, $ticketId ) {
		if( FALSE===strpos(Yii::$app->params['sendEmail'],'Yes'))return;
		//---Get the reciever email
		$result=Yii::$app->db->createCommand('SELECT lastname,firstname,patronymic,personemail from employee where id=:id')->bindValues([':id'=>$receiverId])->queryOne();
		$emailto=$result['personemail'];
		$receivername = $result['lastname'].' '.$result['firstname'].' '.$result['patronymic'];
		unset($result);
		
		//---Get the sender email and name
		$result=Yii::$app->db->createCommand('SELECT lastname,firstname,patronymic,personemail from employee where id=:id')->bindValues([':id'=>$senderId])->queryOne();
		$emailfrom = $result['personemail'];
		$sendername = $result['lastname'].' '.$result['firstname'];
		unset($result);
		
		//---Prepare the Email subject and message text
		$result=Yii::$app->db->createCommand('SELECT ticode,tiaddress from ticket where id=:id')->bindValues([':id'=>$ticketId])->queryOne();
		$emailsubject='Заявка № '.$result['ticode'].' '.$result['tiaddress'];
		$emailbody='ЗАЯВКА    № '. $result['ticode']."\n\nИсполнитель: $receivername\nОтправитель: $sendername\nАдрес : ". $result['tiaddress']."\n\n".
		//rawurlencode("http://195.3.196.246/index.php?r=tickets%2Fview&id=$ticketId");
		"http://195.3.196.246/index.php?r=tickets%2Fview&id=$ticketId";
		unset($result);

		
		//---Sending...
		if(empty($emailfrom))$emailfrom='mailer.cds.glkh@gmail.com';
		try{
		$result=Yii::$app->mailer->compose()
            ->setTo($emailto)
            ->setFrom([$emailfrom => $sendername])
            ->setSubject($emailsubject)
            ->setTextBody($emailbody)
            ->send();
        }catch(\Exception $e){$mailererrortext = $e->getMessage();}
        $mailererrortext = mb_convert_encoding(mb_substr($mailererrortext,0,255),'UTF-8'/*,'UTF-8'*/);

		//print($emailsubject);print($emailbody);
 		Yii::$app->db->createCommand()->insert('emaillog',['emailto'=>$emailto,'emailfrom'=>$emailfrom,'emailsubject'=>$emailsubject,'emailbody'=>$emailbody,'emaillength'=>strlen($emailbody),'emailresult'=>$result ? 'OK':'Error:'.$mailererrortext])->execute();
	}
	/*---171020,did start---*/
	public static function savespart( $ticketId, $data ){
		$opdate = date("Y-m-d H:i:s");
		$spId = $data['spId'];
		$spartlist = Yii::$app->db->createCommand('SELECT id,CONCAT(IFNULL(elspcode,"")," ",elspname) as elspart,elspunit FROM elevatorsparepart WHERE id ='.$spId)->queryOne();
		Yii::$app->db->createCommand()->insert('ticketlog',[
				//'tiltime'       => $opdate,	// server will set time
				'tiltype'       => 'SPORDER',
				'tiltext'		=> 'Заказ МТЦ',
				'tilstatus'     => $data['tistatus'], 
				'tilspcode'	    => $spId,
				'tilspname'     => $spartlist['elspart'],
				'tilspunit'	    => $spartlist['elspunit'],
				'tilspquantity'	=> $data['spNum'],
				'tilticket_id'  => $ticketId,
				'tilsender_id'	=> $data['senderId'],
				'tilsenderdesk_id'	=> $data['senderdeskId'],
				'tilreceiver_id'=> $data['receiverId'],
				'tilreceiverdesk_id'	=> $data['receiverdeskId']
			])->execute();
	}
	/*---171020,did end---*/
	public static function savespartdate($id,$plannedsdate)
	{
		$plannedsdate = $plannedsdate.'T17:00';
		Yii::$app->db->createCommand()->update('ticket',['tisplannedtime'=>$plannedsdate],'id='.$id)->execute();

	}
	public static function deletespart($id)
	{
		Yii::$app->db->createCommand()->delete('ticketlog','id='.$id)->execute();		
	}
}
