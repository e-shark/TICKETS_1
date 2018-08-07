<?php
namespace frontend\controllers;

use Yii;
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
        $model->loaduser($UserID);
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->update($UserID)) {
           		return $this->redirect(['index']);
            }
		}   
        return $this->render('EditUser', [ 'model' => $model ]);
	}

	public function actionDeleteUser($UserID)
	{
		UsersList::DeleteUser($UserID);
		return $this->redirect(Yii::$app->request->referrer);
	}
}

