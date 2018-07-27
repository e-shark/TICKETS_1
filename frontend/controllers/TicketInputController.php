<?php
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\TicketInputForm;
use frontend\models\TicketAddData;
use zxbodya\yii2\galleryManager\GalleryManagerAction;
use frontend\models\Product;
use frontend\models\TicketAction;
use DateTime;
use DateInterval;

class TicketInputController extends Controller
{
    public $tifModel;

	function __construct($id, $module, $config = []) {
		 parent::__construct($id, $module, $config);	
         $this->tifModel = new TicketInputForm();
	}

    // Страница интерфейса ввода заявки	 
	public function actionInputform()
    {
        $this->tifModel->tiObjects = TicketInputForm::getTiObjects();
        $this->tifModel->tiProblems = TicketInputForm::getTiProblems();
        $this->tifModel->tiRegions = TicketInputForm::getTiRegions();
        $this->tifModel->tiExecutantsLas = TicketInputForm::getExecutantsListForLAS();
        $this->tifModel->tiDepsList = TicketInputForm::getDivisionsListForMaster();

        return $this->render( 'inputform', [ 'model' => $this->tifModel ] );
    }

    // Получить список улиц района
    public function actionGetStreetsList()
    {
        $res =  json_encode([]);
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $RegionID =  0 + $data['District'];
                $res = json_encode(TicketInputForm::getStreetsList($RegionID, $data['f_all']));
            }
        }
        return $res;
    }

    // Получить список домов на улице
    public function actionGetFacilityList()
    {
        $res =  json_encode([]);
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $StreetId =  0 + $data['StreetId'];
                $res = json_encode(TicketInputForm::getFacilitiesList($StreetId, $data['f_all']));
            }
        }
        return $res;
    }

    // Получить кол-во подъездов в доме
    public function actionGetPorchesNumber()
    {
        $res = 0;
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $FacilitytId =  0 + $data['facility_id'];
                $res = TicketInputForm::getEntranceNumber($FacilitytId);
            }
        }
        return $res;
    }

    // Получить список возможных неисправностей
    public function actionGetProblemsList()
    {
        $res =  json_encode([]);
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $ObjectId =  0 + $data['ObjectId'];
                $res = json_encode(TicketInputForm::getProblemsList($ObjectId));
            }
        }
        return $res;
    }

    // Получить список подъездов, в которых есть лифты
    public function actionGetEntranceWithElevators($FacilityId = 0, $ObjectId = '000')
    {
        return TicketInputForm::getEntranceWithElevators($FacilityId, $ObjectId);
    }

    // Получить список лифтов в доме в конкретном подъезде
    public function actionGetElevatorsList()
    {
        $res =  json_encode([]);
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $FacilityId =  0 + $data['FacilityId'];
                $EntranceId = 0 +  $data['EntranceId'];
                $ObjectId = $data['ObjectId'];
                if ('001'==$ObjectId)
                  $res = json_encode(TicketInputForm::getElevatorsList($FacilityId, $EntranceId));
                else
                  $res = json_encode(TicketInputForm::getSwichboardList($FacilityId, $EntranceId));
            }
        }
        return $res;
    }

    // Определить, к какому подразделению передавать заявку по лифту
    public function actionGetElevatorDivision()
    {
        $res = [];
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $ElevatorId =  0 + $data['ElevatorId'];
                $ObjectId = $data['ObjectId'];
                $res = TicketInputForm::getElevatorDivision($ElevatorId,$ObjectId);
            }
        }
        return json_encode($res);
    }

    // Внести новую заявку в базу
    public function actionTicketAdd()
    {
        $Ticket = NULL;
        if(Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (! empty($data)){
                $nowdate = date("Y-m-d H:i:s");

                $Ticket = new TicketAddData();
                $Ticket->ticodeex = NULL;
                $Ticket->tipriority = $data['tiPriority'];
                $Ticket->tistatus = $data['tiStatus'];
                $Ticket->tistatustime = $nowdate;
                $Ticket->tiexecutantread = NULL;

                $Ticket->tiincidenttime = $nowdate;
                $Ticket->tiopenedtime = $nowdate;

                /*
                if ('EMERGENCY' == $data['tiPriority']) { $Ticket->tiplannedtime = (new DateTime())->add(new DateInterval('PT30M'))->format("Y-m-d H:i:s"); }
                else { $Ticket->tiplannedtime = (new DateTime())->add(new DateInterval('P3D'))->format("Y-m-d H:i:s"); }
                $Ticket->tiplannedtimenew = (new DateTime())->add(new DateInterval('P3D'))->format("Y-m-d H:i:s"); 
                $Ticket->tiiplannedtime = (new DateTime())->add(new DateInterval('P3D'))->format("Y-m-d H:i:s"); 
                */

                $timespan = TicketAddData::getTickPlanTimeSpan($data['tiProblem']);
                $Ticket->tiplannedtime = (new DateTime())->add(new DateInterval('PT'.$timespan.'S'))->format("Y-m-d H:i:s");
                $Ticket->tiplannedtimenew = $Ticket->tiplannedtime; 
                $Ticket->tiiplannedtime = $Ticket->tiplannedtime; 
                $Ticket->tisplannedtime = NULL;
                $Ticket->ticlosedtime = NULL;


                $Ticket->tiobject_id = $data['tiObject'];
                $Ticket->tiproblemtype_id = $data['tiProblem'];
                $Ticket->tiproblemtext = $data['tiProblemDetails'];
                $Ticket->tidescription = $data['tiComment'];

                $Ticket->tifacility_id = $data['tiFacility'];
                $Ticket->tifacilitycode = $Ticket->getFacilityCod($data['tiFacility']);
                $Ticket->tiregion = $Ticket->getRegionName($data['tiRegion']);
                $Ticket->tiaddress = $Ticket->getAdressStr($data['tiStreet'] ,$data['tiFacility'] ,$data['tiObject'] ,$data['tiElevator'] ,$data['tiEntrance'] , $data['tiApartment']);

                $Ticket->fillOriginator(Yii::$app->user->id);
                //$Ticket->tioriginator = $Ticket->getOriginatorName();
                //$Ticket->tioriginatordesk_id = NULL;`

                $Ticket->ticaller = $data['tiCaller'];
                $Ticket->ticallerphone = $data['tiCallerPhone'];
                $Ticket->ticalleraddress = $data['tiCallerAddres'];
                $Ticket->ticalltype = $data['tiSource'];

                $Ticket->tiresumedtime = NULL;
                $Ticket->tiresulterrorcode = NULL;
                $Ticket->tiresulterrortext = NULL;

                $Ticket->tiequipment_id = $data['tiElevator'];
                $Ticket->fillElevatorDivision($data['tiElevator']); // заполняет tidivision_id и tiobjectcode по id лифта ElevatorID из tiElevator
                //$Ticket->tiobjectcode = инв. номер по $data['tiElevator'];
                //$Ticket->tidivision_id = $data[''];
                //$Ticket->tiexecutant_id = $data[''];

                //if ((!$Ticket::isWorkTime()) ) {
                switch($data['DivisionType']){
                    case 0:  // Задано подразделение для лифта
                        $Ticket->tidesk_id = $Ticket->tidivision_id; 
                        $SMSReciver = TicketAddData::getDivisionMasterId($Ticket->tidesk_id);
                        break;

                    case 2:  // из списка лифтовых подразделений
                        $Ticket->tidesk_id = $data['tiDepSelect']; 
                        $SMSReciver = TicketAddData::getDivisionMasterId($Ticket->tidesk_id);
                        break;

                    case 1:  // из списка ЛАС лифтеров
                        $Ticket->tiexecutant_id = $data['tiExecutant'];
                        $Ticket->tidesk_id = $Ticket->tioriginatordesk_id; 
                        $SMSReciver = $Ticket->tiexecutant_id;
                        break;

                    case 3:  // Задано подразделение ВДЭС
                    case 5:  // из списка подразделений ВДЭС
                        $Ticket->tidesk_id = $data['tiVDESDepSelect']; 
                        $SMSReciver = TicketAddData::getDivisionMasterId($Ticket->tidesk_id);
                        break;

                    case 4:  // из списка ЛАС ВДЭС
                        $Ticket->tiexecutant_id = $data['tiVDESExecutant'];
                        $Ticket->tidesk_id = $Ticket->tioriginatordesk_id; 
                        $SMSReciver = $Ticket->tiexecutant_id;
                        break;
                }

                $tiid = $Ticket->TicketAddNew();
                $Ticket->MakeLogRecord();
                $Ticket->ExportLog($tiid);

                $ta = new TicketAction();
                $ta->sendSMS($SMSReciver, $Ticket->tioriginator_id, $Ticket->recid);
                Yii::warning('SMSReciver= '.$SMSReciver.'  Sender: '.$Ticket->tioriginator_id.'  tiID: '.$Ticket->recid,__METHOD__);
            }
        }
        //return $this->render( 'AddConfirm', ['model' => $Ticket] );
        return $this->redirect(['add-confirm', 'tiId'=>(is_null(Ticket)?0:$Ticket->recid)]);
    }

    // Страница подстверждения ввода заявки
    public function actionAddConfirm($tiId)
    {
        $Ticket = new TicketAddData();
        $Ticket->recid = $tiId;
        $Ticket->getTicketInfo($tiId);
        return $this->render( 'AddConfirm', ['model' => $Ticket] );
    }

    public function actions()
    {
        return [
           'galleryApi' => [
               'class' => GalleryManagerAction::className(),
               // mappings between type names and model classes (should be the same as in behaviour)
               'types' => [
                    'product' => Product::className()
               ]
           ],
        ];
    }

}
    