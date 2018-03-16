<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

class Report_Summary1562 extends Model
{
	public $datefrom;
	public $dateto;
	public $reportpagesize;
	public $district;
	public $status;

	public $provider;		// сводная таблица заявок по районам по лифтам
	public $counters;		// счетчики общего кол-ва неисправностей разного вида

	public function generate($params)
	{
        if (empty($params['datefrom'] )) 
            $params['datefrom'] = "1-".date('m-Y');       
        
 		$f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltbl="SELECT t.tiregion, t.tiobjectcode, count(t.tiobjectcode) as XALL
				,case when t.tiresulterrorcode =0 or t.tiresulterrorcode is null then count(id) end XX 
				,case when t.tiresulterrorcode between 1 and 99 then count(id) end X0 
				,case when t.tiresulterrorcode between 100 and 199 then count(id) end X1 
				,case when t.tiresulterrorcode between 200 and 299 then count(id) end X2 
				,case when t.tiresulterrorcode between 300 and 399 then count(id) end X3 
				,case when t.tiresulterrorcode between 400 and 499 then count(id) end X4 
				,case when t.tiresulterrorcode between 500 and 599 then count(id) end X5 
				,case when t.tiresulterrorcode between 600 and 699 then count(id) end X6 
				,case when t.tiresulterrorcode between 700 and 799 then count(id) end X7 
				,case when t.tiresulterrorcode between 800 and 899 then count(id) end X8 
				,case when t.tiresulterrorcode between 900 and 999 then count(id) end X9 
				,case when t.tiresulterrorcode between 1000 and 1099 then count(id) end X10 
				,case when t.tiresulterrorcode between 1100 and 1199 then count(id) end X11 
				,case when t.tiresulterrorcode between 1200 and 1299 then count(id) end X12 
				,case when t.tiresulterrorcode between 1300 and 1399 then count(id) end X13 
				,case when t.tiresulterrorcode between 1400 and 1499 then count(id) end X14 
				,case when t.tiresulterrorcode between 1500 and 1599 then count(id) end X15 
				,case when t.tiresulterrorcode between 1600 and 1699 then count(id) end X16 
				,case when t.tiresulterrorcode between 1700 and 1799 then count(id) end X17 
				,case when t.tiresulterrorcode between 1800 and 1899 then count(id) end X18 
				,case when t.tiresulterrorcode between 1900 and 1999 then count(id) end X19 
				,case when t.tiresulterrorcode between 2000 and 2099 then count(id) end X20 
				,case when t.tiresulterrorcode between 2100 and 2199 then count(id) end X21 
				,case when t.tiresulterrorcode between 2200 and 2299 then count(id) end X22 
				,case when t.tiresulterrorcode between 2300 and 2399 then count(id) end X23 
				,case when t.tiresulterrorcode between 2400 and 2499 then count(id) end X24 
				,case when t.tiresulterrorcode between 2500 and 2599 then count(id) end X25 
				,case when t.tiresulterrorcode between 2600 and 2699 then count(id) end X26 
				,case when t.tiresulterrorcode between 2700 and 2799 then count(id) end X27 
				,case when t.tiresulterrorcode between 2800 and 2899 then count(id) end X28 
				,case when t.tiresulterrorcode between 2900 and 2999 then count(id) end X29 
				,case when t.tiresulterrorcode between 3000 and 3099 then count(id) end X30 
				,case when t.tiresulterrorcode between 3100 and 3199 then count(id) end X31 
				,case when t.tiresulterrorcode between 3200 and 3299 then count(id) end X32 
				,case when t.tiresulterrorcode between 3300 and 3399 then count(id) end X33 
				,case when t.tiresulterrorcode between 3400 and 3499 then count(id) end X34 
				,case when t.tiresulterrorcode between 3500 and 3599 then count(id) end X35 
				,case when t.tiresulterrorcode between 3600 and 3699 then count(id) end X36 
				,case when t.tiresulterrorcode between 3700 and 3799 then count(id) end X37 
				,case when t.tiresulterrorcode between 3800 and 3899 then count(id) end X38 
				,case when t.tiresulterrorcode between 3900 and 3999 then count(id) end X39 
				,case when t.tiresulterrorcode between 4000 and 5999 then count(id) end X40 
				,case when t.tiresulterrorcode between 6000 and 6999 then count(id) end X60 
				,case when t.tiresulterrorcode >=7000 and t.tiresulterrorcode <> 9999  then count(id) end XM 
				,case when t.tiresulterrorcode = 9999 then count(id) end X99 
				FROM ticket t 
				WHERE t.ticoderemote is not null
					AND t.tiobject_id = 1
					AND t.tiobjectcode>0 $f1sql
				GROUP BY  t.tiregion, t.tiobjectcode 
				ORDER BY  t.tiregion, t.tiobjectcode ";


		$sqlcnt="SELECT ts.tiregion, count(ts.tiobjectcode), sum(XALL) as sXALL
			,sum(XX) as sXX, sum(X0) as sX0, sum(X1) as sX1, sum(X2) as sX2, sum(X3) as sX3, sum(X4) as sX4, sum(X5) as sX5, sum(X6) as sX6, sum(X7) as sX7, sum(X8) as sX8, sum(X9) as sX9
			,sum(X10) as sX10, sum(X11) as sX11, sum(X12) as sX12, sum(X13) as sX13, sum(X14) as sX14, sum(X15) as sX15, sum(X16) as sX16, sum(X17) as sX17, sum(X18) as sX18, sum(X19) as sX19
			,sum(X20) as sX20, sum(X21) as sX21, sum(X22) as sX22, sum(X23) as sX23, sum(X24) as sX24, sum(X25) as sX25, sum(X26) as sX26, sum(X27) as sX27, sum(X28) as sX28, sum(X29) as sX29
			,sum(X30) as sX30, sum(X31) as sX31, sum(X32) as sX32, sum(X33) as sX33, sum(X34) as sX34, sum(X35) as sX35, sum(X36) as sX36, sum(X37) as sX37, sum(X38) as sX38, sum(X39) as sX39
			,sum(X40) as sX40, sum(X60) as sX60, sum(XM) as sXM, sum(X99) as sX99
			FROM ($sqltbl) ts";

		$this->provider = new SqlDataProvider([
			'sql' => $sqltbl,
			'pagination'=>['pageSize'=>$this->reportpagesize],			
		]);

		$this->counters = Yii::$app->db->createCommand($sqlcnt)->queryOne();

		return $provider;	
	}

	public function FillColumnSet(&$ColumnSet, $Name, $Description)
	{
		if (!empty($this->counters['s'.$Name])){
			$ColumnSet[]=[
                'label' =>"<div style='height: 280px; width:20px;'> <div style='position:relative ; top: 260px; transform: rotate(-90deg)'>".str_replace(" ","&nbsp;",$Description)."</div></div>",
                'encodeLabel' => false,
				'content' => function($data) use ($Name){ 
					return empty($data[$Name])?"":$data[$Name]; 
				}, 
			];
		}
	}

}
