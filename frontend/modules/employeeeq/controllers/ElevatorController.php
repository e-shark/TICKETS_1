<?php

namespace frontend\modules\employeeeq\controllers;

use Yii;

use frontend\modules\employeeeq\models\Elevator;
use frontend\modules\employeeeq\models\ElevatorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use frontend\modules\employeeeq\models\Division;
use frontend\modules\employeeeq\models\Employee;

/**
 * ElevatorController implements the CRUD actions for Elevator model.
 */
class ElevatorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Elevator models.
     * @return mixed
     */

     public function actionIndex($id=0)
    { 
        if ($id==0) { $model=new Division(); }
        else { $model=$this->findModel($id); }
        $searchModel = new ElevatorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' =>  $model,
        ] );
    }

   public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Elevator model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Elevator();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Elevator model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Elevator model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSenddivision()
    {
        $id=htmlspecialchars($_POST['name']);//способ принять значение с формы
        if ($id==0) {
            return $this->redirect(['index', 'id' => $id,]);
            } else {// при выборе всех теряется - обход потери страницы
        return $this->redirect(['index', 'ElevatorSearch[eldivision_id]'=>$id,
            'id' => $id,]); }//фильтр таблицы по-выбраному значению
    }

     public function actionDismissal()//Увольнение
    {
        $model = $this->findModel($id);
       
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Elevator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Elevator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Elevator::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена');
    }
}
