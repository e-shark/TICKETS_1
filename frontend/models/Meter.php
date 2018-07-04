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
		$sqltext = "SELECT * FROM powermeterdata where mdatameter_id=".$id." and (mdatadeleted is null) order by mdatatime desc ";
		$result = new SqlDataProvider([
			'sql' => $sqltext,
		]);
		return $result;
	}

	// Добавляет одну запись в таблицу показаний счетчиков
	public function InsertReading($time, $val, $obis, $state, $comment, $filename)
	{
		$result = 0;
		$id = $this->MeterId;

		Yii::$app->db->createCommand()->insert('powermeterdata',[
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
		$time = date("Y-m-d H:i:s");
		if ( is_array($reading) ){
			foreach($reading as $obis=>$val){
				$res = Meter::InsertReading($val, $reading, $obis, '1', NULL, NULL);
			}
		}else{
			$res = Meter::InsertReading($time, $reading, '1.8.0', '1', NULL, $picture);
		}
		return $res;
	}

    public function UploadMeterPhoto($meterid,$recordid,$obis)
    {
        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$meterid.DIRECTORY_SEPARATOR.'R'.$recordid;
        //if ($this->validate()) {
        if (true){	
            if (!is_dir($uploadpath)) 
                if (!mkdir($uploadpath,0777,TRUE))
                    return false;
            $this->imageFile->saveAs( $uploadpath.DIRECTORY_SEPARATOR.$obis. '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }

    public function GetPhotoFileName($recordid){
    	$res = '';
    	if (!empty($recordid)){
	        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$this->MeterId.DIRECTORY_SEPARATOR.'R'.$recordid;
            $res = $uploadpath.DIRECTORY_SEPARATOR.'1.8.0.jpg';
    	}
    	return $res;
    }

}
