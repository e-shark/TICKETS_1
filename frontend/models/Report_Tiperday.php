<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\data\ArrayDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;


class Report_Tiperday extends Model
{
	//public $calltype;
    public $repyear;
    public $repmonth;
    public $calltype;
    public $repdays;
    public $result;
    

	public function generate($params)
	{	
       $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

        //$this->sqls=$sqltext;
        //$this->params=$params;
        
        
        $this->repdays = cal_days_in_month(CAL_GREGORIAN, $this->repmonth, $this->repyear);
        $sqltext="SELECT    tiregion, 
            count(*) as 'Total',
            count( if( day(tiopenedtime)=1 ,1,null)) as '1', 
            count( if( day(tiopenedtime)=2 ,1,null)) as '2', 
            count( if( day(tiopenedtime)=3 ,1,null)) as '3', 
            count( if( day(tiopenedtime)=4 ,1,null)) as '4', 
            count( if( day(tiopenedtime)=5 ,1,null)) as '5', 
            count( if( day(tiopenedtime)=6 ,1,null)) as '6', 
            count( if( day(tiopenedtime)=7 ,1,null)) as '7',
            count( if( day(tiopenedtime)=8 ,1,null)) as '8',
            count( if( day(tiopenedtime)=9 ,1,null)) as '9',
            count( if( day(tiopenedtime)=10 ,1,null)) as '10',
            count( if( day(tiopenedtime)=11 ,1,null)) as '11',
            count( if( day(tiopenedtime)=12 ,1,null)) as '12',
            count( if( day(tiopenedtime)=13 ,1,null)) as '13',
            count( if( day(tiopenedtime)=14 ,1,null)) as '14',
            count( if( day(tiopenedtime)=15 ,1,null)) as '15',
            count( if( day(tiopenedtime)=16 ,1,null)) as '16',
            count( if( day(tiopenedtime)=17 ,1,null)) as '17',
            count( if( day(tiopenedtime)=18 ,1,null)) as '18',
            count( if( day(tiopenedtime)=19 ,1,null)) as '19',
            count( if( day(tiopenedtime)=20 ,1,null)) as '20',
            count( if( day(tiopenedtime)=21 ,1,null)) as '21',
            count( if( day(tiopenedtime)=22 ,1,null)) as '22',
            count( if( day(tiopenedtime)=23 ,1,null)) as '23',
            count( if( day(tiopenedtime)=24 ,1,null)) as '24',
            count( if( day(tiopenedtime)=25 ,1,null)) as '25',
            count( if( day(tiopenedtime)=26 ,1,null)) as '26',
            count( if( day(tiopenedtime)=27 ,1,null)) as '27',
            count( if( day(tiopenedtime)=28 ,1,null)) as '28',
            count( if( day(tiopenedtime)=29 ,1,null)) as '29',
            count( if( day(tiopenedtime)=30 ,1,null)) as '30',
            count( if( day(tiopenedtime)=31 ,1,null)) as '31'
        from ticket where (tidesk_id!=6) $f1sql group by tiregion
        union select 'Итого', 
            count(*) as 'Total',
            count( if( day(tiopenedtime)=1 ,1,null)) as '1', 
            count( if( day(tiopenedtime)=2 ,1,null)) as '2', 
            count( if( day(tiopenedtime)=3 ,1,null)) as '3', 
            count( if( day(tiopenedtime)=4 ,1,null)) as '4', 
            count( if( day(tiopenedtime)=5 ,1,null)) as '5', 
            count( if( day(tiopenedtime)=6 ,1,null)) as '6', 
            count( if( day(tiopenedtime)=7 ,1,null)) as '7',
            count( if( day(tiopenedtime)=8 ,1,null)) as '8',
            count( if( day(tiopenedtime)=9 ,1,null)) as '9',
            count( if( day(tiopenedtime)=10 ,1,null)) as '10',
            count( if( day(tiopenedtime)=11 ,1,null)) as '11',
            count( if( day(tiopenedtime)=12 ,1,null)) as '12',
            count( if( day(tiopenedtime)=13 ,1,null)) as '13',
            count( if( day(tiopenedtime)=14 ,1,null)) as '14',
            count( if( day(tiopenedtime)=15 ,1,null)) as '15',
            count( if( day(tiopenedtime)=16 ,1,null)) as '16',
            count( if( day(tiopenedtime)=17 ,1,null)) as '17',
            count( if( day(tiopenedtime)=18 ,1,null)) as '18',
            count( if( day(tiopenedtime)=19 ,1,null)) as '19',
            count( if( day(tiopenedtime)=20 ,1,null)) as '20',
            count( if( day(tiopenedtime)=21 ,1,null)) as '21',
            count( if( day(tiopenedtime)=22 ,1,null)) as '22',
            count( if( day(tiopenedtime)=23 ,1,null)) as '23',
            count( if( day(tiopenedtime)=24 ,1,null)) as '24',
            count( if( day(tiopenedtime)=25 ,1,null)) as '25',
            count( if( day(tiopenedtime)=26 ,1,null)) as '26',
            count( if( day(tiopenedtime)=27 ,1,null)) as '27',
            count( if( day(tiopenedtime)=28 ,1,null)) as '28',
            count( if( day(tiopenedtime)=29 ,1,null)) as '29',
            count( if( day(tiopenedtime)=30 ,1,null)) as '30',
            count( if( day(tiopenedtime)=31 ,1,null)) as '31'
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