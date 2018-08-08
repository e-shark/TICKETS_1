<?php

namespace frontend\modules\employeeeq\controllers;

use Yii;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\EmployeeSearch;
use frontend\modules\employeeeq\models\Elevator;
use frontend\modules\employeeeq\models\ElevatorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
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
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }
     /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employee model.
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
     * Deletes an existing Employee model.
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
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');//'The requested page does not exist.');
    }

    public function actionMissal($id)//,$data)//Уcтройство на работу
    {
        $model = $this->findModel($id);
       // $model1 = $this->findModel($id);
        $model->isemployed = '1';
        $model->employmentdate = date("y.m.d");
        $model->save();
        //if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        // }
        // return $this->render('view', [
        //     'model' => $model,
        // ]); 
    }
    public function actionDismissal($id)//,$data)//Увольнение
    {
        $model = $this->findModel($id);
        $model1 = $this->findModel($id);
        $model1->isemployed = '0';
        $model1->dismissaldate = date("y.m.d");
        $model1->save();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

      public function actionEmechanic($id,$district=0,$streettype=0,
                                  $street=0,$house=0)
    {
        $model = $this->findModel($id);
        $searchModel = new ElevatorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $eldistrict=$ElevatorSearch[eldistrict];
        //var_dump($eldistrict);
        return $this->render('emechanic', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'eldistrict'=>$district,
            'elstreettype'=>$streettype,
            'elstreetname'=>$street,
            'elhouse'=>$house,
        ]);
    }

    public function actionAppend($id,$district=0,$streettype=0,
                                $street=0,$house=0,$emechanic=0)
    {
        $model = $this->findModel($id);
        $searchModel = new ElevatorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('append', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'eldistrict'=>$district,
            'elstreettype'=>$streettype,
            'elstreetname'=>$street,
            'elhouse'=>$house,
            'emechanic'=>$emechanic,
        ]);
    }

    public function actionSendstreetap()//,$data)//Уcтройство на работу
    {
        $id=htmlspecialchars($_POST['id']);//
        $district = Yii::$app->request->post('district', null);
        $streettype = Yii::$app->request->post('streettype', null);
        $street = Yii::$app->request->post('street', null);
        $house = Yii::$app->request->post('house', null);
        $emechanic= Yii::$app->request->post('emechanic', null);
        return $this->redirect(['append',
            'ElevatorSearch[elperson_id]'=>$emechanic,
            'ElevatorSearch[eldistrict]'=>$district,
            'ElevatorSearch[elstreettype]'=>$streettype,
            'ElevatorSearch[elstreetname]'=>$street,
            'ElevatorSearch[elhouse]'=>$house,
            'id' => $id, 'district'=>$district, 
            'streettype'=>$streettype, 'street'=>$street,
            'house'=>$house, 'emechanic'=>$emechanic]); 
    }
     public function actionSendstreet()
    {
        $id=htmlspecialchars($_POST['id']);//
        //$=htmlspecialchars($_POST['id']);//
        //Дом-house Улица-street Тип улицы streettype/ Район district
        $district = Yii::$app->request->post('district', null);
        $streettype = Yii::$app->request->post('streettype', null);
        $street = Yii::$app->request->post('street', null);
        $house = Yii::$app->request->post('house', null);
        //$par5 = Yii::$app->request->post('elfacility_id', null);
        //if ($id==0) {$id=500; } // если ничего не выбрано
       // return $this->redirect(['index','ElevatorSearch[eldevicetype]'=>$par1,'ElevatorSearch[eldistrict]'=>$par2,'ElevatorSearch[elstreettype]'=>$par3]
        return $this->redirect(['emechanic',
            //'emechanic',
            'ElevatorSearch[eldistrict]'=>$district,
            'ElevatorSearch[elstreettype]'=>$streettype,
            'ElevatorSearch[elstreetname]'=>$street,
            'ElevatorSearch[elhouse]'=>$house,
            'ElevatorSearch[elperson_id]'=>$id, 
            'id' => $id, 'district'=>$district, 
            'streettype'=>$streettype, 'street'=>$street,
            'house'=>$house ]); //id=14&ElevatorSearch[elperson_id]=14
        //} else {// при выборе всех теряется - обход потери страницы
        //  return $this->redirect(['emechanic', 'ElevatorSearch[eldivision_id]'=>$id,'id' => $id,]); }//фильтр таблицы по-выбраному значению
    }
     public function actionSaver($id=0)
    {
      $check=Yii::$app->request->post('selection');
      $ccheck=count($check);
      if ($ccheck>0) 
      {
        $employee=$this->findModel($id);
        $eldivision=$employee->division_id;
        foreach ($check as $index => $checkone) 
        { //код для массового закрепления
          $elmodel=$this->findElmodel($checkone);//находим модель Елеватор
          $elmodel->elperson_id = $id; //закрепляем оборудование за монтажником
          $elmodel->eldivision_id=$eldivision;//вычисляем ид/заклепляем за подразелением
          $elmodel->save();//сохраняем БД
        } 
      }
      return $this->redirect(['append','id' => $id]);
    }

    protected function findElmodel($id)
    {
        if (($model = Elevator::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрошенная страница не существует.');
        //throw new NotFoundHttpException('Запрошенная страница c id='.$id.' не существует.');//'The requested page does not exist.');
    }
     /* public function actionMulti()//пример 
  {
        if ($keyList = Yii::$app->request->post('keyList'))
        {
            $arrKey = explode(',', $keyList);
            //var_dump($arrKey); // Получен массив со значениями
        }
        return false;
  }*/
}
