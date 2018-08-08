<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\employeeeq\models\Occupation;

/**
 * OccupationSearch represents the model behind the search form of `frontend\models\Occupation`.
 */
class OccupationSearch extends Occupation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['occupationname', 'occupationcode'], 'safe'],
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
        $query = Occupation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
           'query' => $query->orderBy(['occupationname' => SORT_ASC]),
        ]);
        $dataProvider->setSort([
            'attributes' => [
                'occupationname'=> [
                    'asc' => ['occupationname' => SORT_ASC],
                    'desc' => ['occupationname' => SORT_DESC],
                    'default' => SORT_DESC,   
                ],
            
            ]
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
        ]);

        $query->andFilterWhere(['like', 'occupationname', $this->occupationname])
            ->andFilterWhere(['like', 'occupationcode', $this->occupationcode]);

        return $dataProvider;
    }
}
