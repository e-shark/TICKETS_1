<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Report_Titotals;


class Report_Repfailures extends Model
{
	public $datefrom;
	public $dateto;
	public $district;
	public $calltype;
	public $reportpagesize;

	public function generate($params)
	{
        $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltext="select count(tiobjectcode) as cnt, tiobjectcode,tiaddress, division.divisionname  from ticket left outer join division on division.id=tidivision_id  where 1 $f1sql group by tiobjectcode  having cnt>1";
		
		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'pagination'=>['pageSize'=>$this->reportpagesize],
			'sort' => [
				'attributes' => [
					'cnt',
				],
				'defaultOrder' => [ 'cnt' => SORT_DESC ],
			],
		]);
		if( 0 === $this->reportpagesize ) $provider->pagination->pageSize = $provider->totalCount; // Show all records
		return $provider;
	}
}
	