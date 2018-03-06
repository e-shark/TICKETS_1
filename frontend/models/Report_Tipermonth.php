<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;


class Report_Tipermonth extends Model
{
	//public $calltype;
    public $repyear;
    public $calltype;
    public $result;
    

	public function generate($params)
	{	
       $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

        //$this->sqls=$sqltext;
        //$this->params=$params;
        
        
        $sqltext="SELECT    tiregion, 
            count(*) as 'Total',
            count( if( month(tiopenedtime)=1 ,1,null)) as '1', 
            count( if( month(tiopenedtime)=2 ,1,null)) as '2', 
            count( if( month(tiopenedtime)=3 ,1,null)) as '3', 
            count( if( month(tiopenedtime)=4 ,1,null)) as '4', 
            count( if( month(tiopenedtime)=5 ,1,null)) as '5', 
            count( if( month(tiopenedtime)=6 ,1,null)) as '6', 
            count( if( month(tiopenedtime)=7 ,1,null)) as '7',
            count( if( month(tiopenedtime)=8 ,1,null)) as '8',
            count( if( month(tiopenedtime)=9 ,1,null)) as '9',
            count( if( month(tiopenedtime)=10 ,1,null)) as '10',
            count( if( month(tiopenedtime)=11 ,1,null)) as '11',
            count( if( month(tiopenedtime)=12 ,1,null)) as '12'
        from ticket where (tidesk_id!=6) $f1sql group by tiregion
        union select 'Итого', 
            count(*) as 'Total',
            count( if( month(tiopenedtime)=1 ,1,null)) as '1', 
            count( if( month(tiopenedtime)=2 ,1,null)) as '2', 
            count( if( month(tiopenedtime)=3 ,1,null)) as '3', 
            count( if( month(tiopenedtime)=4 ,1,null)) as '4', 
            count( if( month(tiopenedtime)=5 ,1,null)) as '5', 
            count( if( month(tiopenedtime)=6 ,1,null)) as '6', 
            count( if( month(tiopenedtime)=7 ,1,null)) as '7',
            count( if( month(tiopenedtime)=8 ,1,null)) as '8',
            count( if( month(tiopenedtime)=9 ,1,null)) as '9',
            count( if( month(tiopenedtime)=10 ,1,null)) as '10',
            count( if( month(tiopenedtime)=11 ,1,null)) as '11',
            count( if( month(tiopenedtime)=12 ,1,null)) as '12'
        from ticket where (tidesk_id!=6) $f1sql";
        
        $this->result = Yii::$app->db->createCommand($sqltext)->queryAll();
        $provider = new ArrayDataProvider([
            'allModels' => $this->result,
        ]);
/*
        $provider = new SqlDataProvider([
            'sql' => $sqltext,
            'key' => 'no',
            'pagination' => ['pageSize' => 32,],
        ]);*/
        return $provider;
    }
    public function isUserFitter(){return false;}
}