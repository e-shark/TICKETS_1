<?php
//namespace frontend\controllers;
namespace frontend\modules\employeeeq\controllers;

use yii\web\Controller;
use yii\helpers\Url;

class DefaultController extends Controller
{
    public function actionIndex()
    {
    	$url = Url::toRoute(['/facilityeq']);//формируем url 
        return $this->redirect($url);//переходим на список справочников
	}
}
