<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;


class Report_Oos extends Model
{
	public $datefrom;
	public $dateto;
	public $district;
	public $calltype;
	public $reportpagesize;

	public function generate($params)
	{
        $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltext="SELECT ticket.id, tiincidenttime,ticode,tiaddress,tiobjectcode,divisionname, TIMESTAMPDIFF(HOUR,tiincidenttime,now()) as ooshours from ticket left join division on division.id=tidivision_id where tiproblemtype_id=3 and (tistatus not like '%COMPLETE') and (TIMESTAMPDIFF(HOUR,tiincidenttime,now()) > 24) $f1sql";
		$oprights = Tickets::getUserOpRights();

		//---Prepare the sql statement for tickets according to the user rights
		//if(FALSE !== $oprights )$sqltext = $sqltext.' and tidivision_id = '.$oprights[division_id];
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'pagination'=>['pageSize'=>$this->reportpagesize],
			'sort' => [
				'attributes' => [
					'tiincidenttime',
					'ooshours',
				],
				'defaultOrder' => [ 'tiincidenttime' => SORT_ASC ],
			],
		]);
		if( 0 === $this->reportpagesize ) $provider->pagination->pageSize = $provider->totalCount; // Show all records
		return $provider;
	}
}
	