<?php

namespace frontend\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\web\UploadedFile;

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
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],		// Для валидации загружаемой фотографии
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

	// Добавить фотографию к показаниям
	// Сохраняет файл с фотографией, и в базу записывает расширение файла (только расширение, патамушто имя вычисляется по формуле)
    public function AddReadingPhoto($recordid, $filenamebody)
    {
    	$path = $this->MakePhotoFilePath();
        if ($this->validate()) {
        	$ext = $this->imageFile->extension;
            if (!is_dir($path)) 
                if (!mkdir($path,0777,TRUE))
                    return false;
            $sres = $this->imageFile->saveAs( $path.$filenamebody.'.'.$ext);
            if ($sres)		// если удалось записать файл
				Yii::$app->db->createCommand("UPDATE powermeterdata SET mdatafile = '{$ext}' WHERE id=".$recordid)->execute();
        }
    }

    // Сохраняем показания с картинкой (если она есть)
    // MeterData - цифра показаний
    // MeterPhoto - файл картинки (объект null|yii\web\UploadedFile, загруженый с помощью getInstanceByName() )
    public function SaveReading($MeterData, $MeterPhoto)
    {
		$oprights = Tickets::getUserOpRights();
		if( !empty($oprights) ) {
			$now = date("Y-m-d H:i:s");
			$who = $oprights['id'];
            $this->imageFile = $MeterPhoto;
            $obis = '1.8.0';
           	$rid = $this->InsertReading($who, $now, $obis, $MeterData, '1', NULL, NULL);
           	if (!empty($rid)){
           		if ($this->validate())				// проверяем картинку (точнее исходное название файла с картинкой)
           			$this->AddReadingPhoto( $rid, $this->MakePhotoFileNameBody($rid, $obis, $now) );
           	}
           	// ЦОЙ ЖИВ !!!
        }
    }

    // Получить тело имени файла (без пути и расширения)
    public function MakePhotoFileNameBody($recordid, $obis, $datetime )
    {
    	$timestamp = preg_replace('~\D+~','',$datetime);  	// убираем из строки все, окромя цифр
    	$res .= 'R'.$recordid.'_'.$obis.'_'.$timestamp;		// скрещиваем номер записи, обис код и дату записи
    	return $res;
    }

    // Получить путь, где складываем фото показаний
	public function MakePhotoFilePath()
	{
    	$res = Yii::getAlias('@app').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$this->MeterId.DIRECTORY_SEPARATOR;
    	return $res;
	}

    // Получить имя файла, где лежит на сервере фотография показаний
    public function GetReadingPhotoFileName($recordid){
    	$res = '';
    	if (!empty($recordid)){
	    	$sqltext = "SELECT mdatatime, mdatacode, mdatafile FROM powermeterdata WHERE id =".$recordid.";";
   			$rec = Yii::$app->db->createCommand($sqltext)->queryOne();	
			if (!empty($rec)){
				$res = $this->MakePhotoFilePath().$this->MakePhotoFileNameBody($recordid, $rec['mdatacode'], $rec['mdatatime']).'.'.$rec['mdatafile'];
			}
    	}
    	return $res;
    }

}
