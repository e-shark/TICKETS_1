<?php

namespace frontend\modules\employeeeq\controllers;

use Yii;
use frontend\modules\employeeeq\models\Occupation;
use frontend\modules\employeeeq\models\OccupationSearch;
use frontend\modules\employeeeq\models\EmployeeSearch;
use frontend\modules\employeeeq\models\Employee;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * OccupationController implements the CRUD actions for Occupation model.
 */
class OccupationController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all Occupation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OccupationSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams );
         return $this->render
         ('index', 
            [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
         //   'sorter' => 'occupationname',
            ]
         );
    }

    /**
     * Displays a single Occupation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model1 = $this->findModel($id);
        $url = Url::toRoute(['view','EmployeeSearch[occupation_id]'=>$id,'id'=>$id]);
        if ($model1->load(Yii::$app->request->post()) && $model1->save()) {
            return $this->redirect( $url );
        }

        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', 
        [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Occupation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Occupation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'EmployeeSearch[occupation_id]'  => $model->id,'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Occupation model.
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
     * Deletes an existing Occupation model.
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

    /**
     * Finds the Occupation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Occupation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Occupation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');//'The requested page does not exist.');
    }
}
 