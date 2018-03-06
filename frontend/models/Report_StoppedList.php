<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;

class Report_StoppedList extends Model
{
	public $district;
	public $datefrom;
	public $dateto;
	public $tifindstr;

	public function generate($params)
	{
 		$f1sql = Report_Titotals::fillparamsfiltet1($this,$params);

		$sqltext="SELECT ticket.id, ticket.tiaddress, ticket.tiobjectcode,ticode, tiregion, tiopenedtime, tioosbegin, tioosend, tiplannedtimenew, TIMESTAMPDIFF(HOUR,tioosbegin,coalesce(tioosend,now())) as oostime, oostypetext, tiproblemtypetext, tidescription, tiproblemtext, streetname, fabuildingno, elporchno, elporchpos, elinventoryno from ticket left join elevator on ticket.tiobjectcode=elevator.elinventoryno 
 left join ticketproblemtype on ticket.tiproblemtype_id =ticketproblemtype.id 
 left join oostype on ticket.tioostype_id=oostype.id
 left join facility on ticket.tifacility_id =facility.id 
 left join street on facility.fastreet_id =street.id
 where tioosbegin is not null $f1sql";

		$provider = new SqlDataProvider([
			'sql' => $sqltext,
			'key' => 'id',
			'sort' => [
				'attributes' => [
					'tiincidenttime',
					'ooshours',
				],
				'defaultOrder' => [ 'tiincidenttime' => SORT_ASC ],
			],
		]);
		return $provider;	
	}


}
