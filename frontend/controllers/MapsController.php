<?php
namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Maps_TicketsFilter;
use yii\helpers\Url;

class MapsController extends Controller
{

	public function actionIndex()	
    {
    	$model = new Maps_TicketsFilter();
        $model->fillparams(Yii::$app->request->queryParams);
        if (empty(Yii::$app->request->queryParams['datefrom'] )) 
            $model->datefrom = date('d-M-y');
    	return $this->render( 'index',['model'=>$model] );
    }

	public function actionGetMarkerList()	
    {
    	$provider = json_encode([]);
    	if(Yii::$app->request->isPost) {
	    	$model = new Maps_TicketsFilter();
	    	$provider = json_encode( $model->generate(Yii::$app->request->post()) );
    	}
    	return $provider;
	}

	public function actionGoToTiketsList()	
    {
    	$params = Yii::$app->request->queryParams;
    	unset($params['xr']);						// убираем из запроса старый маршрут
    	unset($params['r']);						// убираем из запроса текущий маршрут
    	if (!empty($params['facilityid'] ))
    		$params['tifindstr'] = Maps_TicketsFilter::getFilterAddressStr($params['facilityid']);
		return $this->redirect(Url::to(['/reports/ticketslist']+ $params),302);
    }

}


