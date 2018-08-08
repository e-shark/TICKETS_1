<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\employeeeq\models\Elevator;
use frontend\modules\employeeeq\models\Facility;

/**
 * ElevatorSearch represents the model behind the search form of `frontend\models\Elevator`.
 */
class ElevatorSearch extends Elevator
{
    /**
     * {@inheritdoc}
     */
    //public $district; //искуственное поле Район
    public $eldistrict;
    public $elstreetname;
    public $elstreettype;
    public $elhouse;
 
    public function rules()
    {
        return [
            [['id', 'elremoteid', 'eldevicetype', 'elload', 'elstops', 'elporchno', 'elrtu_id', 'elfacility_id', 'eldivision_id', 'elperson_id'], 'integer'],
            [['elserialno', 'elmodel', 'eldate', 'eldoortype', 'eltype', 'elporchpos', 'elinventoryno', 'elregyear'], 'safe'],
            [['elspeed'], 'number'],
            [['eldistrict', 'elstreetname', 'elstreettype', 'elhouse'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Elevator::find();
        $query->leftJoin('facility', 'facility.id = elevator.elfacility_id')->all(); 
        $query->leftJoin('street', 'facility.fastreet_id = street.id')->all(); 
        $query->leftJoin('district', 'facility.fadistrict_id = district.id')->all();
        /*->select('customer_id, SUM(amount) as order_amount')
        ->groupBy('customer_id');*/
        // add conditions that should always apply here
      //  $query->leftJoin([
       // 'orderSum'=>$subQuery 
    //], 'orderSum.customer_id = id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'elremoteid' => $this->elremoteid,
            'eldevicetype' => $this->eldevicetype,
            'eldate' => $this->eldate,
            'elload' => $this->elload,
            'elspeed' => $this->elspeed,
            'elstops' => $this->elstops,
            'elporchno' => $this->elporchno,
            'elrtu_id' => $this->elrtu_id,
//            'elfacility_id' => $this->elfacility_id,
            'eldivision_id' => $this->eldivision_id,
            'elperson_id' => $this->elperson_id,
            //'elfacility_id' => $this->district,
            //'district'=>$this->elfacility->fadistrict_id,
            'facility.fadistrict_id' => $this->eldistrict,
            'facility.fabuildingno' => $this->elhouse
        ]);
        
        $query->andFilterWhere(['like', 'elserialno', $this->elserialno])
            ->andFilterWhere(['like', 'elmodel', $this->elmodel])
            ->andFilterWhere(['like', 'eldoortype', $this->eldoortype])
            ->andFilterWhere(['like', 'eltype', $this->eltype])
            ->andFilterWhere(['like', 'elporchpos', $this->elporchpos])
            ->andFilterWhere(['like', 'elinventoryno', $this->elinventoryno])
            ->andFilterWhere(['like', 'elregyear', $this->elregyear])
            ->andFilterWhere(['like', 'street.streettype', $this->elstreettype])
            ->andFilterWhere(['like', 'street.streetnameru', $this->elstreetname]);

        //$query->andWhere('lastname LIKE "%' . $this->fullName
        //$query
        //$query->andFilterWhere([
          //  'division_id' => $this->division ]);
        return $dataProvider;
    }
}
