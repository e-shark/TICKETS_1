<?php

namespace frontend\modules\employeeeq\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\employeeeq\models\Division;
use yii\data\Sort;

/**
 * DivisionSearch represents the model behind the search form of `frontend\models\Division`.
 */
class DivisionSearch extends Division
{
    /**
     * @inheritdoc
     */
    public $divisionnameup; //искуственно созданое поле
    //public $divisionemployee;//искуственно созданое поле

    public function rules()
    {
        return [
            [['id', 'division_id', 'divisioncompany_id'], 'integer'],
            [['divisionname', 'divisionfullname', 'divisioncode', 'divisioncodesvc', 'divisiondate'], 'safe'],
            [['divisionnameup',/*'divisionemployee'*/],'safe']

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
        $query = Division::find();
       /* $sort = new Sort
            ([  'attributes' => 
                [ 
                    'divisionname' =>
                    [ 'asc' => ['divisionname' => SORT_ASC],
                      'desc' => ['divisionname' => SORT_DESC],
                      'default' => SORT_DESC,
                    ],
                     'divisionfullname' => 
                    [  
                       'asc' => ['divisionfullname' => SORT_ASC],
                       'desc' => ['divisionfullname' => SORT_DESC],
                       'default' => SORT_DESC,
                    ],
                      'divisioncode' => 
                    [  
                       'asc' => ['divisioncode' => SORT_ASC],
                       'desc' => ['divisioncode' => SORT_DESC],
                       'default' => SORT_DESC,
                    ],   

                ]
            ]); */
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider
        ([
            //'query' => $query,
            'query' => $query->orderBy(['divisionname' => SORT_ASC]),
        ]);
                $dataProvider->setSort
            ([ 'attributes' => 
                [ 'divisionname' =>
                    [ 'asc' => ['divisionname' => SORT_ASC],
                      'desc' => ['divisionname' => SORT_DESC],
                      'default' => SORT_DESC,
                    ], 
                  /*'divisionemployee'=>
                      ['asc' => ['divisionemployee' => SORT_ASC,'divisionemployee' => SORT_ASC],
                       'desc' => ['divisionemployee' => SORT_DESC,'divisionemployee' => SORT_ASC],
                       'default' => SORT_DESC,
                    ],*/
                 /* 'divisionfullname' => 
                    [  
                       'asc' => ['divisionfullname' => SORT_ASC],
                       'desc' => ['divisionfullname' => SORT_DESC],
                       'default' => SORT_DESC,
                       'label'=>'divisionfullname',
                    ],
                  'divisioncode' => 
                    [  
                       'asc' => ['divisioncode' => SORT_ASC],
                       'desc' => ['divisioncode' => SORT_DESC],
                       'default' => SORT_DESC,
                    ], */ 
                ] 
            ]);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
   /*     $query->andFilterWhere([
            'id' => $this->id,
            'divisiondate' => $this->divisiondate,
            'division_id' => $this->division_id,
            'divisioncompany_id' => $this->divisioncompany_id,
        ]);

        //$query->andFilterWhere([
          //  'division_id' => $this->division ]);
        
/*        $query->andFilterWhere(['divisioncode' => $this->divisioncode])
            ->andFilterWhere(['like', 'divisionname', $this->divisionname])
            ->andFilterWhere(['like', 'divisionfullname', $this->divisionfullname])
            //->andFilterWhere(['like', 'divisioncode', $this->divisioncode])
            
            ->andFilterWhere(['like', 'divisioncodesvc', $this->divisioncodesvc]);

     /*   $query->andWhere('divisionname LIKE "%' .$this->divisionnameup . '%" ' .
          'OR divisionfullname LIKE "%' . $this->divisionnameup . '%"');
*/
        return $dataProvider;
    }
}