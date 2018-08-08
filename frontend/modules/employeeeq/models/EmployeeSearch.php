<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\employeeeq\models\Employee;

/**
 * EmployeeSearch represents the model behind the search form of `frontend\models\Employee`.
 */
class EmployeeSearch extends Employee
{
    public $fullName; //искуственно созданое поле
   // public $division; //искуственно созданое поле
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remoteid', 'postcode', 'statusdisability', 'statuschernobyl', 'user_id', 'occupation_id', 'division_id'], 'integer'],
            [['firstname', 'patronymic', 'lastname', 'empcode','personcode', 'passportno', 'passportdata', 'personaddress', 'currentaddress', 'personphone', 'personphone1', 'personemail', 'personurl', 'sex', 'birthday', 'married', 'education', 'employmentdate', 'dismissaldate', 'skillscategory', 'skillsrank', 'certprofessional', 'certmedical', 'certnarcology', 'certpsych', 'certcriminal', 'statusmilitary', 'lastjob', 'isemployed', 'employmenttype', 'oprights'], 'safe'],
            [['salary', 'rate'], 'number'],
            [['fullName'], 'safe'],
           // [['division'],'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Employee::find();

        // add conditions that should always apply here
        //if (sort==0) {
            $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['isemployed'=>SORT_DESC,'lastname' => 
                  SORT_ASC, 'firstname' => SORT_ASC, 'patronymic'=>SORT_ASC]) 
                ]);
        //'query' => $query,
        //'sort'=> ['defaultOrder' => ['isemployed'=>SORT_DESC,'lastname' => 
         //       SORT_ASC, 'firstname' => SORT_ASC, 'patronymic'=>SORT_ASC]] ]);
     
        //} 
    /**'query' => $someQuery->orderBy(['date' => SORT_DESC])
     * Настройка параметров сортировки
     * Важно: должна быть выполнена раньше $this->load($params)
     */
    $dataProvider->setSort([
        'attributes' => [
            'id',
            'fullName' => [
                'asc' => ['lastname' => SORT_ASC, 'firstname' => SORT_ASC, 'patronymic'=>SORT_ASC],
                'desc' => ['lastname' => SORT_DESC, 'firstname' => SORT_DESC, 'patronymic'=>SORT_DESC],
                //'label' => 'Ф.И.О.',
                'default' => SORT_DESC//ASC
            ],
            //'empcode',
            //'occupation_id',
//            'division_id',
          /*  'division' => 
                [  
                'asc' => ['division_id'=>SORT_ASC],
                'desc' => ['division_id'=>SORT_DESC],
                //'label' => 'HGh',
                'default' => SORT_ASC
                ] */
    ],   
     ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'remoteid' => $this->remoteid,
            'postcode' => $this->postcode,
            'birthday' => $this->birthday,
            'employmentdate' => $this->employmentdate,
            'dismissaldate' => $this->dismissaldate,
            'salary' => $this->salary,
            'rate' => $this->rate,
            'certprofessional' => $this->certprofessional,
            'certmedical' => $this->certmedical,
            'certnarcology' => $this->certnarcology,
            'certpsych' => $this->certpsych,
            'certcriminal' => $this->certcriminal,
            'statusdisability' => $this->statusdisability,
            'statuschernobyl' => $this->statuschernobyl,
            'user_id' => $this->user_id,
            'occupation_id' => $this->occupation_id,
            'empcode'=>$this->empcode,
            'division_id' => $this->division_id,
            //'division' => $this->division,
            'oprights'=> $this->oprights,
        ]);

        $query->//andFilterWhere(['like', 'firstname', $this->firstname])
            //->andFilterWhere(['like', 'patronymic', $this->patronymic])
            //->andFilterWhere(['like', 'lastname', $this->lastname])
            //->
            andFilterWhere(['like', 'personcode', $this->personcode])
            ->andFilterWhere(['like', 'passportno', $this->passportno])
            ->andFilterWhere(['like', 'passportdata', $this->passportdata])
            ->andFilterWhere(['like', 'personaddress', $this->personaddress])
            ->andFilterWhere(['like', 'currentaddress', $this->currentaddress])
            ->andFilterWhere(['like', 'personphone', $this->personphone])
            ->andFilterWhere(['like', 'personphone1', $this->personphone1])
            ->andFilterWhere(['like', 'personemail', $this->personemail])
            ->andFilterWhere(['like', 'personurl', $this->personurl])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'married', $this->married])
            ->andFilterWhere(['like', 'education', $this->education])
            ->andFilterWhere(['like', 'skillscategory', $this->skillscategory])
            ->andFilterWhere(['like', 'skillsrank', $this->skillsrank])
            ->andFilterWhere(['like', 'statusmilitary', $this->statusmilitary])
            ->andFilterWhere(['like', 'lastjob', $this->lastjob])
            ->andFilterWhere(['like', 'isemployed', $this->isemployed])
            ->andFilterWhere(['like', 'employmenttype', $this->employmenttype])
            ->andFilterWhere(['like BINARY', 'oprights', $this->oprights])
            //->andFilterWhere(['like', 'division', $this->division_id])
            ;


        $query->andFilterWhere([
            'division_id' => $this->division_id ]);
            //'streetcode' => $this->streetcode,
            //'streetlocality_id' => $this->streetlocality_id,

        // фильтр по имени
         $query->andWhere('lastname LIKE "%' . $this->fullName . '%" ' .
              'OR firstname LIKE "%' . $this->fullName . '%"' . 
              'OR patronymic LIKE "%' . $this->fullName . '%" '); 



        // фильтр по должности
         //$query->andWhere([]
            //   .
            //   'OR firstname LIKE "%' . $this->fullName . '%"' . 
            // 'OR patronymic LIKE "%' . $this->fullName . '%" ' 
           //   );

        return $dataProvider;
    }
}