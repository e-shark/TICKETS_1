<?php
namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use frontend\models\UsersList;
use frontend\models\SignupForm;
use frontend\models\UserUpdateForm;

class UsersController extends Controller
{
    public function actionIndex()
    {
    	$UsersList = new UsersList();
    	$filter = UsersList::FillFilterParams($UsersList, Yii::$app->request->queryParams);
    	$provider = $UsersList->GetUsersList($filter);
        return $this->render('index',['model'=>$UsersList,'provider'=>$provider]);
    }

    public function actionAddNew()
	{
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
           		return $this->redirect(['index']);
            }
        }
        return $this->render('EnterNewUser', [ 'model' => $model ]);
	}

	public function actionEditUser($UserID)
	{
        $model = new UserUpdateForm();

		// Запоминаем, откуда пришли, чтобы можно было потом вернуться с сохранение фильтров    
        if (empty(Yii::$app->request->post()['firstref'])) {
            if (empty(Yii::$app->request->referrer))
                $model->firstref = urlencode( Url::toRoute(['users/index']) );
            else
                $model->firstref = urlencode( Yii::$app->request->referrer );
        }else  $model->firstref = Yii::$app->request->post()['firstref']; 
//Yii::warning("************************************************model***********************[\n".json_encode($model)."\n]");


        // подгружаем данные по юзеру и введенные поля, и все это пытаемся валидировать и сохранить
        if ( !empty($model->loaduser($UserID)) ){
	        if ($model->load(Yii::$app->request->post())) {
	            if ($user = $model->update($UserID)) {
//Yii::warning("************************************************firstref***********************[\n".urldecode($model->firstref)."\n]");
					//return "! ERROR !";
	           		return $this->redirect( urldecode($model->firstref) );
	           		//return $this->redirect( "http://lift/index.php?r=users%2Findex&username=&email=&oprightsstr=&sort=username");
//Yii::warning("************************************************---------------***********************[\n".urldecode($model->firstref)."\n]");
	            }
			}   

			// прорисовываем форму, если сохранения небыло
	        return $this->render('EditUser', [ 'model' => $model ]);
    	}else {
    		return $this->redirect( urldecode($model->firstref) );
    	}
	}

	public function actionDeleteUser($UserID)
	{
		UsersList::DeleteUser($UserID);
		return $this->redirect(Yii::$app->request->referrer);
	}
}

