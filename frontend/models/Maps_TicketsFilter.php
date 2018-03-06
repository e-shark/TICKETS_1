<?php
namespace frontend\models;
use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use frontend\models\Tickets;
use frontend\models\Report_Titotals;


class Maps_TicketsFilter extends Model
{


	public $datefrom;
	public $dateto;
	public $district;
	public $calltype;
    public $status;
    public $tifindstr;
    public $tiexecutant;
    

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
            'district'=>'Район: ',
			'calltype'=>'Источник: ',
            'datefrom'=>'Дата от: ',
            'dateto'=>'Дата по: ',
        ];
    }

    public function isUserFitter(){return false;}

    public function fillparams($params)
    {
        Report_Titotals::fillparamsfiltet1($this,$params);
    }

	public function generate($params)
	{	
        $f1sql = Report_Titotals::fillparamsfiltet1($this,$params);
        
        $sqltext = 'SELECT ticket.id, ticket.ticode, ticket.ticoderemote, ticket.tiopenedtime, (now()>ticket.tiplannedtimenew) as obsflag, ticket.tidescription, ticket.tiaddress, ticket.tistatus, ticket.tioriginator, CONCAT(lastname," ", firstname) as executant, divisionname, ticket.tifacility_id, falatitude, falongitude FROM ticket left join employee on employee.id=tiexecutant_id left join division on division.id = tidivision_id left join facility on ticket.tifacility_id=facility.id ';
        $sqltext .= ' where (falatitude is not null) and (falongitude is not null)';
        $sqltext .= $f1sql;
        $sqltext .= ' order by falatitude, falongitude, ticket.tiopenedtime desc; ';
        //$sqltext .= ' order by ticket.tifacility_id , ticket.tiopenedtime; ';  // ??? дает ошибку "2027: Malformed packet"

//Yii::warning('======'.$sqltext,__METHOD__);		

		$provider = Yii::$app->db->createCommand($sqltext)->queryAll();	

		return $provider;
    }

    public static function getFilterAddressStr( $tifacility_id )
    {
        $facility = Yii::$app->db->createCommand('SELECT id, faaddressno, fastreet_id FROM facility WHERE id = :fid ;')->bindValues([':fid' => $tifacility_id])->queryOne();
        $streetid = $facility['fastreet_id'];
        $Street = Yii::$app->db->createCommand('SELECT id, streetname,streettype FROM street WHERE id = :sid ;')->bindValues([':sid' => $streetid])->queryOne();
        $addr = $Street['streettype'].' '.$Street['streetname'].' '.$facility['faaddressno'];
        return $addr;
    }
}
	
