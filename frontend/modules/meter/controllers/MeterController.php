<?php
namespace frontend\modules\meter\controllers;

use yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use frontend\models\TicketInputForm;
use frontend\modules\meter\models\Meter;
use frontend\modules\meter\models\MetersList;
use frontend\modules\meter\models\FitterMetersList;

class MeterController extends Controller
{

    // Отображение списка всех счетчиков
	public function actionIndex()	
    {
        $meterlist = new MetersList();
        //$filter = MetersList::FillFilterParams($meterlist, Yii::$app->request->queryParams);
        $provider = $meterlist->GetMeterList();
        return $this->render( 'MeterList', ['provider'=>$provider, 'model'=>$meterlist]  );
    }

    // Отображает список счетчиков для монтера
    public function actionFitterMetersList()
    {
        $isfitter = false;
        $meterlist = new FitterMetersList();
        //$meterlist->district = '6310136600';
        //$meterlist->assigned = true;
        if( FALSE !== strpos($meterlist->oprights['oprights'],'F' ) ) {
            // определили, что пользователь - это механик
            $isfitter = true;
            $meterlist->district = FitterMetersList::getFitterDistrictCodeBySB( $meterlist->oprights['id'] );
        } 
        $filter = $meterlist->FillFilterParams($meterlist, Yii::$app->request->queryParams, $isfitter);
        $provider = $meterlist->GetMeterList($filter);
        return $this->render( 'FitterMetersList', ['provider'=>$provider, 'model'=>$meterlist]  );
    }   

    // Отображение паспорта счетчика
	public function actionMeterInfo($MeterId = 0 )	
    {
    	$meter = new Meter($MeterId);
        $passport = $meter->GetMeterPassport($MeterId);
        $meterdata = $meter->GetReadings($MeterId);

        return $this->render( 'MeterInfo', [ 'model' => $meter, 'passport'=>$passport, 'meterdata'=>$meterdata] );
    }

    // Добавляет запись показаний для счетчика
    public function actionAddReading( )  
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if ( empty($data['MeterTime']) ) $data['MeterTime'] = 0;
            try{
                $MeterDateTime = Yii::$app->formatter->asDatetime($data['MeterDate'],'yyyy-MM-dd');
                $MeterDateTime .= sprintf( " %02d:00", $data['MeterTime'] );
            } 
            catch(\Exception $e){ 
                $MeterData=date("Y-m-d H:i:s");
            }
            $MeterId = $data['MeterId'];
            $MeterData = floor( $data['MeterData'] );
            $MeterPhoto = UploadedFile::getInstanceByName('imageFile');
            $RefUrl = $data['RefUrl'];
            if ( (!empty($MeterId)) && (!is_null($MeterData)) ) {
                $meter = new Meter($MeterId);
                $meter->SaveReading($MeterDateTime, $MeterData, $MeterPhoto);
            }
        }   
        if (empty($RefUrl))
            return $this->redirect(['meter-info','#'=>'meterdata','MeterId'=>$MeterId]);//$this->redirect(['view','id'=>$id]);
        else
            return $this->redirect(urldecode($RefUrl));
    }

    // Удаляет запись показаний
    public function actionDeleteReading( $MeterId=0, $ReadingId=0 )  
    {
        if ( (!empty($MeterId)) && (!empty($ReadingId)) ) {
            $meter = new Meter($MeterId);
            $meter->DeleteReading($ReadingId);
        }
        return $this->redirect(['meter-info','#'=>'meterdata','MeterId'=>$MeterId]);
    }

    // Удаляет все текущие показания (все показания с 10 по текущее число)
    public function actionDeleteAllCurrent( $MeterId=0, $firstref = null )  
    {
        if ( !empty($MeterId) ) {
            $meter = new Meter($MeterId);
            $meter->DeleteAllCurrentReading($MeterId);
        }
        return $this->redirect(Yii::$app->request->referrer.(empty($firstref)?"":"&firstref=".$firstref));
        //return $this->redirect([Yii::$app->request->referrer]);
        
    }

    // Получить фотографию показаний
    public function actionGetMeterPhoto( $MeterId=0, $ReadingId=0 )  
    {
        if ( (!empty($MeterId)) && (!empty($ReadingId)) ) {
            $meter = new Meter($MeterId);
            $filename = $meter->GetReadingPhotoFileName($ReadingId);
            if (file_exists($filename)) {
                $path_parts = pathinfo($filename);                                                      // вынимаем имя файла (без пути) 
                Yii::$app->response->sendFile($filename, $path_parts["basename"], ['inline'=>"1"]);
            }   
        };    
    }

    // Форма ввода показаний по счетчику
    public function actionEnterReading( $MeterId=0, $firstref = null )  
    {
        if (!empty($MeterId)) {
            $meter = new Meter($MeterId);
            $passport = $meter->GetMeterPassport($MeterId);
            $LastReading = $meter->GetLastReading($MeterId);
            if (empty($firstref)) {
                if (empty(Yii::$app->request->referrer))
                    $firstref = Url::toRoute(['meter/fitter-meters-list']);
                else
                    $firstref = Yii::$app->request->referrer;
                $firstref = urlencode($firstref);
            }
//Yii::warning("************************************************LastReading***********************[\n".json_encode($LastReading)."\n]");            
            return $this->render( 'MeterEnterData', [ 'model' => $meter, 'passport'=>$passport, 'LastReading'=>$LastReading , 'firstref'=>$firstref]);
        } else 
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    // Редактор паспорта счетчика (существующего или нового)
    // Если задан $MeterId, будет произведено редактирование паспорта.
    // Если $MeterId не задан, будет добавлен в базу новый счетчик.
    public function actionMeterEdit($MeterId = null)  
    {
        $meter = new Meter($MeterId);

        if (!empty($MeterId)) {
            $passport = $meter->GetMeterPassport($MeterId);
        }else{
            $passport['districtcode'] = '6310136600';       // Для нового счетчика район по умолчанию - 'Киевский'
        }
        $mtypes = Meter::GetMeterTypesOptionsList();
        $Regions = TicketInputForm::getTiRegions();
        $Streets = ArrayHelper::map(TicketInputForm::getStreetsList( $passport['districtcode'] ),'id','text');
        if (empty($MeterId)) $passport['fastreet_id'] = key($Streets);
        $Fasilities = ArrayHelper::map(TicketInputForm::getFacilitiesList( $passport['fastreet_id'] ),'id','text');
        return $this->render( 'MeterEdit', ['model'=>$meter, 'passport'=>$passport, 'mtypes'=>$mtypes, 
                              'regions'=>$Regions, 'streets'=>$Streets, 'fasilities'=>$Fasilities ] );
    }

    // Ввод нового счетчика
    public function actionAddMeter( )  
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $MeterId = Meter::SavePassport($data);
            $meter = new Meter($MeterId);
            if (!empty($data['metecalibrationdata']))
                Meter::UpdateCalibrationDate($MeterId, $data['metecalibrationdata']);
        }
        return $this->redirect(['meter-info','MeterId'=>$MeterId]);
    }

}

