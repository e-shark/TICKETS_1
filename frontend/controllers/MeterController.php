<?php
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Meter;
use frontend\models\MeterList;
use frontend\models\UploadImage;
use yii\web\UploadedFile;
use yii\web\Response;


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
        $imagemodel = new UploadImage();
        $passport = $meter->GetMeterPassport($MeterId);
        //if (empty($passport )) $this->redirect(['index']);
    	//$Meter->Readings = Meter::GetMeterPassport($MeterId);
        $meterdata = $meter ->GetReadings($MeterId);
        return $this->render( 'MeterInfo', [ 'model' => $meter, 'passport'=>$passport, 'meterdata'=>$meterdata,'imagemodel'=>$imagemodel] );

    }

    public function actionAddReading($MeterId )  
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $MeterId = $data['MeterId'];
            $MeterData =  $data['MeterData'];
            if ( (!empty($MeterId)) && (!empty($MeterData)) ) {
                $meter = new Meter($MeterId);
                $img = new UploadImage();
                $RecordId = $meter->AddReadingSimple($MeterData);
                if ($RecordId > 0) {
                    //$img->imageFile = UploadedFile::getInstance($img, 'imageFile');
                    $img->imageFile = UploadedFile::getInstanceByName('imageFile');
                Yii::warning("---XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX----".$img->imageFile."*****************");
                    if ($img->uploadMeterPhoto($MeterId,$RecordId,'1.8.0')) {
                        // file is uploaded successfully
                        return $this->redirect(['meter-info','MeterId'=>$MeterId]);;
                    }
                }
            }
        }   
        return $this->redirect(['meter-info','MeterId'=>$MeterId]);//$this->redirect(['view','id'=>$id]);
    }

    public function actionGetMeterPhoto($MeterId=0,$RecId=0)  
    {
        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'ReadingsPhoto'.DIRECTORY_SEPARATOR.'M'.$MeterId.DIRECTORY_SEPARATOR.'R'.$RecId;
        $filename = $uploadpath.DIRECTORY_SEPARATOR.'1.8.0.jpg';
        if (file_exists($filename)) {

            //header('X-Accel-Redirect: ' . $filename);
            //header('Content-Type: application/octet-stream');
            //header('Content-Disposition: attachment; filename=' . basename($filename));
            //Yii::$app->response->headers->set('Content-Type', mime_content_type($filename));
            Yii::$app->response->sendFile($filename);
            //Yii::warning("-------------------------------------------------------------  ".$filename);

            /*
            $response = \Yii::$app->getResponse();
            $m = mime_content_type($filename);
            $response->headers->set('Content-Type', mime_content_type($filename));
            $response->format = Response::FORMAT_RAW;
            if ( !is_resource($response->stream = fopen($filename, 'r')) ) {
                //throw new NotFoundHttpException('Ничего не найдено');
                Yii::warning("---XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX----  ".$filename);
            }            
            return $response->send();
            */

          }        
    }


}

