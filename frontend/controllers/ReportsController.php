<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\Report_Oos;
use frontend\models\Report_Titotals;
use frontend\models\Report_Ticketslist;
use frontend\models\Report_Repfailures;
use frontend\models\Report_Tiperday;
use frontend\models\Report_Tipermonth;
use frontend\models\Report_Tilas;
use frontend\models\Report_Titotals1562;
use frontend\models\Report_Iteralog;
use frontend\models\Report_StoppedList;
use frontend\models\Report_StoppedSum;
use frontend\models\Report_StoppedCount;
use frontend\models\Report_ElevatorsList;

class ReportsController extends Controller
{
    public function init(){
        $this->layout = 'report_layout.php';
        parent::init();
    }    
	public function actionIndex()	
    {
            return $this->render( 'index' );
    }
	public function actionOosnow()	
    {
    	$model = new Report_Oos();
    	$provider = $model->generate(Yii::$app->request->queryParams);
    	return $this->render( 'oosnow',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTitotals()
    {
        $model = new Report_Titotals();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'titotals',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTicketslist()
    {
        $model = new Report_Ticketslist();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'ticketslist',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionRepfailures()
    {
        $model = new Report_Repfailures();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'repfailures',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTiperday()
    {
        $model = new Report_Tiperday();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'tiperday',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTipermonth()
    {
        $model = new Report_Tipermonth();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'tipermonth',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTilas()
    {
        $model = new Report_Tilas();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'tilas',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionTitotals1562()
    {
        $model = new Report_Titotals1562();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'titotals1562',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionIteralog()
    {
        $model = new Report_Iteralog();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'iteralog',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionStoppedList()
    {
        $model = new Report_StoppedList();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'stoppedlist',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionStoppedSum()
    {
        $model = new Report_StoppedSum();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'stoppedsum',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionStoppedCount()
    {
        $model = new Report_StoppedCount();
        $model->AutoFillIntervals(Yii::$app->request->queryParams['repyear']);
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'stoppedcount',['provider'=>$provider, 'model'=>$model] );
    }
    public function actionElevatorsList()
    {
        $model = new Report_ElevatorsList();
        $provider = $model->generate(Yii::$app->request->queryParams);
        return $this->render( 'elevatorslist',['provider'=>$provider, 'model'=>$model] );
    }
}