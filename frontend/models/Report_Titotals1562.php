<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;


class Report_Titotals1562 extends Model
{
	public $datefrom;
	public $dateto;
	

    public function rules()
    {
        return [
			//[['dateto','datefrom'],'required'],
            [['dateto','datefrom'],'date','format'=>'php: d-m-Y'],
        ];
    }
       public function attributeLabels()
    {
        return [
            'datefrom'=>'Дата от: ',
            'dateto'=>'Дата по: ',
        ];
    }

    public function generate($params)
	{	
		if( empty($params['datefrom'] ) ) $params['datefrom'] = '01-'.date('m-Y');
		$fsql = Report_Titotals::fillparamsfiltet1($this,$params);
		if( empty($params['datefrom']))$this->datefrom = '01-'.date('m-Y');
		//Yii::warning('READ==='.$this->datefrom,__METHOD__);
		$sqltext = "
		SELECT (case when tistatusremote like 'Закр%' then 1 when tistatusremote like 'Вып%' then 2 when tistatusremote like 'При%' then 3 else 4 end) as n,
			tistatusremote, 
		    tistatus, 
		    count(*) as total, 
		    count(if(tiregion like 'Інд%',1,null)) as 'Ind',
		    count(if(tiregion like 'Ки%',1,null)) as 'Kyi',
		    count(if(tiregion like 'Мос%',1,null)) as 'Mos',
		    count(if(tiregion like 'Нем%',1,null)) as 'Nem',
		    count(if(tiregion like 'Нов%',1,null)) as 'Nov',
		    count(if(tiregion like 'Осн%',1,null)) as 'Osn',
		    count(if(tiregion like 'Шев%',1,null)) as 'She',
		    count(if(tiregion like 'Сло%',1,null)) as 'Slo'
		from ticket where ticalltype like '1562' $fsql group by tistatus,tistatusremote 
		union 
		select 100 as n,
			'Итого', 
			'', 
		    count(*) as total, 
		    count(if(tiregion like 'Інд%',1,null)) as 'Ind',
		    count(if(tiregion like 'Ки%', 1,null)) as 'Kyi',
		    count(if(tiregion like 'Мос%',1,null)) as 'Mos',
		    count(if(tiregion like 'Нем%',1,null)) as 'Nem',
		    count(if(tiregion like 'Нов%',1,null)) as 'Nov',
		    count(if(tiregion like 'Осн%',1,null)) as 'Osn',
		    count(if(tiregion like 'Шев%',1,null)) as 'She',
		    count(if(tiregion like 'Сло%',1,null)) as 'Slo'
		from ticket where ticalltype like '1562' $fsql  order by n,tistatus";

		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
		]);
		return $provider;
	}
}
	