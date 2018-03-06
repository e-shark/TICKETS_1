<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

class Report_StoppedCount extends Model
{
	//public $datefrom;
	//public $dateto;
	public $repyear;
	private $_intervals=[];

    public function __construct($config = [])
    {
 		$this->repyear = date("Y");

        parent::__construct($config);
    }  

	//	Очищает список интервалов
	public function ClearIntervals(){$this->intervals=[];}

	//	Добавляет интервал в список интервалов
	//  $from, $to - DateTime (например результат mktime)
	public function AddInterval($from, $to)
	{
		$this->_intervals[] = array(
				"from" => $from,
				"to" => $to
			);
	}

	public function AutoFillIntervals($Year)
	{
		if (empty($Year)) $Year=date("Y");
		$now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$sy = mktime(0, 0, 0, 1,  1, $Year);
        $this->AddInterval( mktime(0, 0, 0, 1,  1, $Year-1), mktime(0, 0, 0, 1,  1, $Year)-1);
        if ($now > $t = strtotime( "01-01-".$Year." +3 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +4 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +5 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +6 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +7 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +8 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +9 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +10 week +1 day" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +6 month" )-1) $this->AddInterval( $sy, $t );
        if ($now > $t = strtotime( "01-01-".$Year." +1 year" )-1) $this->AddInterval( $sy, $t );
        else $this->AddInterval( $sy, $now );
    }

	public function GetIntervals()
	{
		//Yii::warning("\n------------------\n".$this->_intervals);
		return $this->_intervals;
	}

	public function generate($params)
	{
 		$f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

 		if (!empty($this->_intervals))
 			$t_intervals = &$this->_intervals;
 		else{
 			$t_intervals = array(
 					["from" => mktime(0, 0, 0, 1,  1, date("Y")-1),
 					 "to" =>   mktime(0, 0, 0, 1,  1, date("Y"))-1],
 					["from" => mktime(0, 0, 0, 1,  1, date("Y")),
 					 "to" =>   mktime(0, 0, 0, date("m"), date("d"), date("Y"))]
 				);

 		}
 		$timemin = mktime(0, 0, 0, 1,  1, "2038");
		$timemax = mktime(0, 0, 0, 1,  1, "1977");  		
		foreach($t_intervals as $itrvl){
			if ($itrvl["from"] < $timemin) $timemin = $itrvl["from"];
			if ($itrvl["to"] < $timemin) $timemin = $itrvl["to"];
			if ($itrvl["from"] > $timemax) $timemax = $itrvl["from"];
			if ($itrvl["to"] > $timemax) $timemax = $itrvl["to"];
		}
		unset($itrvl);
		$iftimemin = Yii::$app->formatter->asDatetime($timemin,'yyyy-MM-dd H:i:s');
		$iftimemax = Yii::$app->formatter->asDatetime($timemax,'yyyy-MM-dd H:i:s');

		$sel = "";											// перечень условий выборки (интервалов)
		$cs = "";											// перечень столбцов в результате (для соответствующих интервалов)
		foreach ($t_intervals as $key => $itrvl) {
			$iffrom = Yii::$app->formatter->asDatetime($itrvl["from"],'yyyy-MM-dd H:i:s');
			$ifto = Yii::$app->formatter->asDatetime($itrvl["to"],'yyyy-MM-dd H:i:s');
			$sel1 .= ", sum(t.cnt{$key}) as sum{$key}";	
			$sel2 .= ", sum(fi{$key}) as cnt{$key}";	
			$ifcase .= "\n, case when ( tiopenedtime >= '$iffrom' and tiopenedtime < '$ifto' ) then 1 end as fi{$key} ";
		}
		unset($itrvl);
		$sqltext .= "\nFROM ticket";

		$sqltext = "SELECT districtname, allcount {$sel1} FROM 
(SELECT count(e.id) as allcount, d.districtname as districtname FROM elevator e
left join facility f on e.elfacility_id = f.id
left join district d on f.fadistrict_id = d.id
group by d.districtname) r
left join
(
 SELECT tiregion, tiobjectcode $sel2 from	
 (
  SELECT tiregion,  tiobjectcode {$ifcase} 
  FROM ticket
  WHERE (tiobject_id = 1) AND (tioosbegin is not null) 
  AND tiobject_id = 1 AND tiopenedtime >='$iftimemin' AND tiopenedtime <= '$iftimemax' 
 ) x
 group by tiobjectcode
) t
on t.tiregion = r.districtname
group by districtname ";

//!!!!! where tioosbegin is not null

//Yii::warning("\n----------------------------------------------------------------\n".$sqltext."\n----------------------------------------------------------------\n");


		$provider = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $provider;	
	}


}
