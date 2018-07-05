<?php
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Meter;
use frontend\models\MeterList;
use yii\web\Response;
use yii\web\UploadedFile;

class MeterController extends Controller
{

	public function actionIndex()	
    {
        $meterlist = new MeterList();
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
            if ( (!empty($MeterId)) && (!empty($MeterData)) ) {
                $meter = new Meter($MeterId);
                $meter->SaveReading($MeterData, $MeterPhoto);
            }
        }   
        return $this->redirect(['meter-info','#'=>'meterdata','MeterId'=>$MeterId]);//$this->redirect(['view','id'=>$id]);
    }

    // Удаляет запись показаний
    public function actionDeleteReading( $MeterId, $ReadingId=0 )  
    {
        if ( (!empty($MeterId)) && (!empty($ReadingId)) ) {
            $meter = new Meter($MeterId);
            $meter->DeleteReading($ReadingId);
        }

        return $this->redirect(['meter-info','#'=>'meterdata','MeterId'=>$MeterId]);
    }

    // Получить фотографию показаний
    public function actionGetMeterPhoto($MeterId=0,$RecId=0)  
    {
        if ( (!empty($MeterId)) && (!empty($RecId)) ) {
            $meter = new Meter($MeterId);
            $filename = $meter->GetReadingPhotoFileName($RecId);
            if (file_exists($filename)) {
                Yii::$app->response->sendFile($filename);
            }   
        };    
    }

    public function actionEnterReading( $MeterId )  
    {
    }

}

