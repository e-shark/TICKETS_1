<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

class Report_StoppedSum extends Model
{
	public $datefrom;
	public $dateto;

	public function generate($params)
	{
 		$f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltext='SELECT x.tiregion as RIG, 
sum(x4) as SUM4, sum(x5) as SUM5, sum(x6) as SUM6, 
sum(x7) as SUM7, sum(x8) as SUM8, sum(x9) as SUM9, 
sum(x10) as SUM10, sum(x11) as SUM11, sum(x12) as SUM12, 
sum(x13) as SUM13, sum(x14) as SUM14, sum(x15) as SUM15, 
sum(x16) as SUM16, sum(xM16) as MOR16, sum(xx) as SUMALL FROM
(SELECT t.tiregion, t.tiobjectcode, count(t.id), 
case when (count(*) <=4) then count(*) end as x4, 
case when (count(*) =5) then count(*) end as x5,
case when (count(*) =6) then count(*) end as x6,
case when (count(*) =7) then count(*) end as x7,
case when (count(*) =8) then count(*) end as x8,
case when (count(*) =9) then count(*) end as x9,
case when (count(*) =10) then count(*) end as x10,
case when (count(*) =11) then count(*) end as x11,
case when (count(*) =12) then count(*) end as x12,
case when (count(*) =13) then count(*) end as x13,
case when (count(*) =14) then count(*) end as x14,
case when (count(*) =15) then count(*) end as x15,
case when (count(*) =16) then count(*) end as x16,
case when (count(*) >16) then count(*) end as xM16,
count(*) as xx
from ticket t 
where tioosbegin is not null
group by t.tiobjectcode) x
group by x.tiregion
union 
select "Итого" as RIG, 
sum(x4) as SUM4, sum(x5) as SUM5, sum(x6) as SUM6, 
sum(x7) as SUM7, sum(x8) as SUM8, sum(x9) as SUM9, 
sum(x10) as SUM10, sum(x11) as SUM11, sum(x12) as SUM12, 
sum(x13) as SUM13, sum(x14) as SUM14, sum(x15) as SUM15, 
sum(x16) as SUM16, sum(xM16) as MOR16, sum(xx) as SUMALL FROM
(SELECT "Итого", t.tiobjectcode, count(t.id), 
case when (count(*) <=4) then count(*) end as x4, 
case when (count(*) =5) then count(*) end as x5,
case when (count(*) =6) then count(*) end as x6,
case when (count(*) =7) then count(*) end as x7,
case when (count(*) =8) then count(*) end as x8,
case when (count(*) =9) then count(*) end as x9,
case when (count(*) =10) then count(*) end as x10,
case when (count(*) =11) then count(*) end as x11,
case when (count(*) =12) then count(*) end as x12,
case when (count(*) =13) then count(*) end as x13,
case when (count(*) =14) then count(*) end as x14,
case when (count(*) =15) then count(*) end as x15,
case when (count(*) =16) then count(*) end as x16,
case when (count(*) >16) then count(*) end as xM16,
count(*) as xx from ticket t 
 where tioosbegin is not null
group by t.tiobjectcode) x';


		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'RIG',
		]);
		return $provider;	
	}


}
