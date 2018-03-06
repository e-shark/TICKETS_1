<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Report_Titotals;


class Report_Iteralog extends Model
{
	public $datefrom;
	public $dateto;
	public $district;
	public $calltype;
	public $tifindstr;
	public $reportpagesize;

	public function generate($params)
	{
        $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltext="SELECT 
				recordtime,ticket_id,ticode,ticalltype,ticode1562,tistatuslogged, tiopenedtime,tiregion,
				rdescription,oostype.oostypetext as oostypetext,oost1.oostypetext as roostypetext,
				CASE 
					when rstatus_id=1 then 'Новая'
					when rstatus_id=9 then 'В работе'
					when rstatus_id=11 then 'Закрыта'
					when rstatus_id=5 then 'Распределена'
					when rstatus_id=2 then 'Отклонена'
					when rstatus_id=13 then 'Работа окончена'
					else '???'
				END as rstatus_id,
				rcreated,rturnoff_time,rturnon_time,rturnon_plan_time,
				employee.lastname as person,emp1.lastname as rperson,
				txattempts,txrequest,txresult,txtime,isexportdone
			from exportiteralog 
				left  join ticket  on exportiteralog.ticket_id=ticket.id  
				left  join employee  on exportiteralog.person_id=employee.id  
				left  join employee as emp1 on exportiteralog.rperformer_id=emp1.remoteid  
				left join oostype on tioostype_id=oostype.id
				left join oostype as oost1 on exportiteralog.rmalfunction_id=oost1.oostypecode
			where 1 $f1sql ";
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'pagination'=>['pageSize'=>$this->reportpagesize],
			'sort' => [
				'attributes' => [
					'recordtime',
					'isexportdone',
				],
				'defaultOrder' => [ 'recordtime' => SORT_DESC ],
			],
		]);
		if( 0 === $this->reportpagesize ) $provider->pagination->pageSize = $provider->totalCount; // Show all records
		return $provider;
	}
}
	