<?php
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Meter;
use frontend\models\MetersList;
use yii\web\Response;
use yii\web\UploadedFile;

class MeterController extends Controller
{

	public function actionIndex()	
    {
        $meterlist = new MetersList();
        //$filter = MetersList::FillFilterParams($meterlist, Yii::$app->request->queryParams);
    	// Тут когда-нибудь будет список всех счетчиков с фильтром поиска
    	// return "This page is under construction";
        $provider = $meterlist->GetMeterList();
        return $this->render( 'MeterList', ['provider'=>$provider, 'model'=>$meterlist]  );

    }

	public function actionMeterInfo($MeterId = 0 )	
    {
    	$meter = new Meter($MeterId);
        $passport = $meter->GetMeterPassport($MeterId);
        $meterdata = $meter ->GetReadings($MeterId);

        return $this->render( 'MeterInfo', [ 'model' => $meter, 'passport'=>$passport, 'meterdata'=>$meterdata] );

    }

    // Добавляет запись показаний для счетчика
    public function actionAddReading( )  
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $MeterId = $data['MeterId'];
            $MeterData = $data['MeterData'];
            $MeterPhoto = UploadedFile::getInstanceByName('imageFile');
            $RefUrl = $data['RefUrl'];
            if ( (!empty($MeterId)) && (!empty($MeterData)) ) {
                $meter = new Meter($MeterId);
                $meter->SaveReading($MeterData, $MeterPhoto);
            }
        }   
        if (empty($RefUrl))
            return $this->redirect(['meter-info','#'=>'meterdata','MeterId'=>$MeterId]);//$this->redirect(['view','id'=>$id]);
        else
            return $this->redirect($RefUrl);
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

    // Получить фотографию показаний
    public function actionGetMeterPhoto( $MeterId=0, $ReadingId=0 )  
    {
        if ( (!empty($MeterId)) && (!empty($ReadingId)) ) {
            $meter = new Meter($MeterId);
            $filename = $meter->GetReadingPhotoFileName($ReadingId);
            if (file_exists($filename)) {
                Yii::$app->response->sendFile($filename);
            }   
        };    
    }

    // Ввод показаний по счетчику
    public function actionEnterReading( $MeterId=0 )  
    {
        if (!empty($MeterId)) {
            $meter = new Meter($MeterId);
            $passport = $meter->GetMeterPassport($MeterId);
            return $this->render( 'MeterEnterData', [ 'model' => $meter, 'passport'=>$passport, 'refurl'=>(Yii::$app->request->referrer ?: Yii::$app->homeUrl) ]);
        } else 
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    // Редактор паспорта счетчика (существующего или нового)
    public function actionMeterEdit($MeterId=null)  
    {
        $meter = new Meter($MeterId);
        if (!empty($MeterId))
            $passport = $meter->GetMeterPassport($MeterId);
        $mtypes = Meter::GetMeterTypesOptionsList();
        return $this->render( 'MeterEdit', ['model'=>$meter, 'passport'=>$passport, 'mtypes'=>$mtypes] );
    }

    // Ввод нового счетчика
    public function actionAddMeter( )  
    {
        $meter = new Meter();
        $MeterId = 1;
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $MeterId = $data['MeterId'];
        }
        return $this->redirect(['meter-info','MeterId'=>$MeterId]);
    }


}

