<?php

namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

class Meter extends Model
{
    const READINGSPATH  = 'data'.DIRECTORY_SEPARATOR.'ReadingsPhoto';		// Директория (относительно приложения), куда  будут складываться фотографии показаний счетчика

    public $imageFile;
    public $MeterId;

	function __construct($id = 0,  $config = []) {
		parent::__construct($config);	
		$this->MeterId = $id;
	}

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

	//	Заполнить поля паспортных данных по счетчику
	public function GetMeterPassport($id)
	{
		$sqltext = "SELECT pm.* ,
 		concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF(IFNULL(pm.meterporchno,0), concat(' п.',pm.meterporchno),'') ) as addrstr 
		FROM powermeter pm
		join facility fa on fa.id = pm.meterfacility_id 
		join street st on st.id=fa.fastreet_id
		WHERE pm.id=".($id)." ;";
		$res = Yii::$app->db->createCommand($sqltext)->queryOne();	
		return $res;
	}

	// Получить массив показаний по счетчику
	public function GetReadings($id)
	{
		//$sqltext = "SELECT * FROM powermeterdata where mdatameter_id=".$id." and (mdatadeltime is null) order by mdatatime desc ";
		$sqltext = "SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) as employee, pm.* FROM powermeterdata pm left join  employee e on pm.mdatawho=e.id where mdatameter_id={$id} and (mdatadeltime is null) order by mdatatime desc ";
		$result = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $result;
	}

	// Добавляет одну запись в таблицу показаний счетчиков
	public function InsertReading($who, $time, $obis, $val, $state, $comment, $filename)
	{
		$result = 0;
		$id = $this->MeterId;

		Yii::$app->db->createCommand()->insert('powermeterdata',[
			'mdatawho' => $who,
			'mdatatime' => $time,
			'mdata' => $val,
			'mdatacode' => $obis,
			'mdatameterstate' => $state,
			'mdatacomment' => $comment,
			'mdatameter_id' => $id,
			'mdatafile' => $filename,
		])->execute();    

		$result = intval(Yii::$app->db->getLastInsertID());

		return $result;
	}

	// Добавляет данные показаний счетчика
	// В качестве входного парамета чсло - показание А+ (тогда будет вставлено одно значение с обис кодом "1.8.0")
	// либо массив пар обискод=>значение (тогда будут вставлены все значения с соответствующими обис кодами)
	public function AddReadingSimple($reading, $picture=NULL)
	{
		$res = 0;
		$oprights = Tickets::getUserOpRights();
		if( !empty($oprights) ) {
			$now = date("Y-m-d H:i:s");
			$who = $oprights['id'];
			if ( is_array($reading) ){
				foreach($reading as $obis=>$val){
					$res = Meter::InsertReading($who, $now, $obis, $val, '1', NULL, NULL);
				}
			}else{
				$res = Meter::InsertReading($who, $now, '1.8.0', $reading, '1', NULL, $picture);
			}
		}
		return $res;
	}

	// Удаление записи с показаниями по счетчику
	public function DeleteReading($recordid)
	{
		$oprights = Tickets::getUserOpRights();
		if( !empty($oprights) ) {
			$now = date("Y-m-d H:i:s");
			$who = $oprights['id'];
			$sql = "UPDATE powermeterdata SET mdatadeltime = '{$now}', mdatadelwho = {$who} WHERE id=".$recordid;
			Yii::$app->db->createCommand($sql)->execute();
		}
	}

	// Загрузить в базу фотографию показаний
    public function UploadMeterPhoto($meterid,$recordid,$obis)
    {
        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$meterid.DIRECTORY_SEPARATOR.'R'.$recordid;
        $vres = $this->validate();
        Yii::warning("==============================[".$vres."]=====");
        if ($vres) {
        //if (true){	
            if (!is_dir($uploadpath)) 
                if (!mkdir($uploadpath,0777,TRUE))
                    return false;
            $this->imageFile->saveAs( $uploadpath.DIRECTORY_SEPARATOR.$obis. '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }

    // Получить имя файла, где лежит на сервере фотография показаний
    public function GetPhotoFileName($recordid){
    	$res = '';
    	if (!empty($recordid)){
	        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$this->MeterId.DIRECTORY_SEPARATOR.'R'.$recordid;
            $res = $uploadpath.DIRECTORY_SEPARATOR.'1.8.0.jpg';
    	}
    	return $res;
    }

}
