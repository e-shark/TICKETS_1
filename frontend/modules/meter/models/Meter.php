<?php

namespace frontend\modules\meter\models;

use yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use frontend\models\Tickets;

class Meter extends Model
{
    const READINGSPATH  = 'MeterDialPhoto';		// Директория (в дирректории данных), куда  будут складываться фотографии показаний счетчика
    const CalibrationOBIS = 'C.2.5';										// ОБИС код параметра "дата кпоследней поверки" (калибровки)

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
 		concat(' ',ifnull(st.streettype,''),' ', ifnull(st.streetname,''),' ', ifnull(fa.faaddressno,''), IF( (pm.meterporchno!=''), concat(' п.',pm.meterporchno),'') ) as addrstr ,
 		ds.districtcode, fa.fastreet_id, fa.faaddressno  
		FROM powermeter pm
		join facility fa on fa.id = pm.meterfacility_id 
		join street st on st.id=fa.fastreet_id
		join district ds on ds.id = fa.fadistrict_id
		WHERE pm.id=".($id)." ;";
		$res = Yii::$app->db->createCommand($sqltext)->queryOne();	
		if (!empty($res)){
			$sqltext = "SELECT mdatatime FROM powermeterdata WHERE mdatacode='".Meter::CalibrationOBIS."' AND (mdatameter_id={$id}) ORDER BY mdatatime DESC;";
			$lastcalibrdate = Yii::$app->db->createCommand($sqltext)->queryOne()['mdatatime'];
			$res['metecalibrationdata'] = $lastcalibrdate;
		}
		return $res;
	}

	// Получить массив показаний по счетчику
	public function GetReadings($id)
	{
		//$sqltext = "SELECT * FROM powermeterdata where mdatameter_id=".$id." and (mdatadeltime is null) order by mdatatime desc ";
		$sqltext = "SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) as employee, pm.* FROM powermeterdata pm left join  employee e on pm.mdatawho=e.id where mdatameter_id={$id} and (mdatadeltime is null) and (mdatacode='1.8.0') order by mdatatime desc, id desc ";
		$result = new SqlDataProvider([ 'sql' => $sqltext ]);
		return $result;
	}

	// Получить последние показания по счетчику по счетчику
	public function GetLastReading($mid)
	{
		if (empty(Yii::$app->params['MeterAccauntingPeriodDayOfMonth'])) $dateperiod = 10;		// дата начала расчетного периода каждого месяца 
		else $dateperiod = Yii::$app->params['MeterAccauntingPeriodDayOfMonth'];
		$sqltext=  "SELECT ppp.mdatameter_id, ppp.mdatatime, ppp.mdata, ppp.mdatafile, (SELECT concat(e.lastname,' ',e.firstname,' ',e.patronymic) FROM employee e, powermeterdata pm  WHERE pm.mdatawho=e.id AND  pm.id = gg.id ) mwho, ppp.mdatafile, ppp.mdatameterstate, ppp.mdatacomment, ppp.id rec_id, ppp.mdatadeltime, ppp.mdatacode
					FROM powermeterdata ppp,
					  (SELECT MAX(pp.id) id 
      				   FROM (	SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime >= :DP AND mdatadeltime IS NULL ) pp, 
				 			( SELECT MAX(p.mdatatime) t, p.mdatameter_id id
							  FROM (SELECT * FROM powermeterdata WHERE mdatacode = :OBIS AND mdatatime >= :DP AND  mdatadeltime IS NULL) p 
                              GROUP BY p.mdatameter_id
                 			) g 
					   WHERE pp.mdatameter_id = g.id AND pp.mdatatime = g.t GROUP BY g.id) gg
					WHERE ppp.id = gg.id AND mdatameter_id = :MID ;" ;

		$TS = Yii::$app->formatter->asDatetime( mktime(0, 0, 0, date("m"), $dateperiod, date("Y")) ,'yyyy-MM-dd H:i:s');
		if (date("d") < $dateperiod)
			$TS = Yii::$app->formatter->asDatetime( strtotime( $TS." -1 month" ) ,'yyyy-MM-dd H:i:s');
   		$cmd = Yii::$app->db->createCommand($sqltext);
   		$cmd ->bindValues([':MID'=>$mid, ':OBIS'=>'1.8.0', ':DP'=>$TS]);
//Yii::warning("************************************************cmd***********************[\n".$cmd->rawSql."\n]");
		return $cmd->queryOne();
	}

	// Добавляет одну запись в таблицу показаний счетчиков
	public static function InsertReading($meterId, $who, $time, $obis, $val, $state, $comment, $filename)
	{
		$result = 0;
		if (!empty($meterId)) {
			Yii::$app->db->createCommand()->insert('powermeterdata',[
				'mdatawho' => $who,
				'mdatatime' => $time,
				'mdata' => $val,
				'mdatacode' => $obis,
				'mdatameterstate' => $state,
				'mdatacomment' => $comment,
				'mdatameter_id' => $meterId,
				'mdatafile' => $filename,
			])->execute();    
			$result = intval(Yii::$app->db->getLastInsertID());
		}
		return $result;
	}


	// Обновить дату последней поверки счетчика
	public static function UpdateCalibrationDate($MeterId, $Date)
	{
		try{ $dateiso=Yii::$app->formatter->asDatetime($Date,'yyyy-MM-dd'); }
		catch(\Exception $e){ $dateiso=null;}
		if ( (!empty($MeterId)) && (!empty($dateiso)) ) {
			$sqltext = "SELECT id FROM powermeterdata WHERE mdatacode='".Meter::CalibrationOBIS."' AND (mdatameter_id={$MeterId}) AND mdatatime='{$dateiso}';";
			$id = Yii::$app->db->createCommand($sqltext)->queryOne()['id'];
			if (empty($id)){
				$oprights = Tickets::getUserOpRights();
				if( !empty($oprights) ) {
					Meter::InsertReading( $MeterId, $oprights['id'], $dateiso, Meter::CalibrationOBIS, NULL, 1, NULL, NULL );
				}
			}
		}	
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

	// Удаление записи с показаниями за текущий период (с 10 числа по текущее)
	public function DeleteAllCurrentReading()
	{
		$oprights = Tickets::getUserOpRights();
		if( !empty($oprights) && !empty($this->MeterId) ) {
			if (empty(Yii::$app->params['MeterAccauntingPeriodDayOfMonth'])) $dateperiod = 10;		// дата начала расчетного периода каждого месяца 
			else $dateperiod = Yii::$app->params['MeterAccauntingPeriodDayOfMonth'];
			$TS = Yii::$app->formatter->asDatetime( mktime(0, 0, 0, date("m"), $dateperiod, date("Y")) ,'yyyy-MM-dd H:i:s');
			if (date("d") < $dateperiod)
				$TS = Yii::$app->formatter->asDatetime( strtotime( $TS." -1 month" ) ,'yyyy-MM-dd H:i:s');
			$now = date("Y-m-d H:i:s");
			$who = $oprights['id'];
			$sql = "UPDATE powermeterdata SET mdatadeltime = :now, mdatadelwho = :who WHERE mdatameter_id =:mid and mdatatime >= :ts ;";
			$cmd = Yii::$app->db->createCommand($sql);
			$cmd->bindValues([ ':mid'=>0+$this->MeterId, 'ts'=>$TS, 'now'=>$now, 'who'=>$who ]);
//Yii::warning("************************************************cmd***********************[\n".$cmd->rawSql."\n]");
			$cmd->execute();
		}
	}

	// Добавить фотографию к показаниям
	// Сохраняет файл с фотографией, и в базу записывает расширение файла (только расширение, патамушто имя вычисляется по формуле)
    public function AddReadingPhoto($recordid)
    {
    	$fullfilename = $this->GetReadingPhotoFileName($recordid);
    	$path_parts = pathinfo($fullfilename);
    	$path = $path_parts["dirname"];
    	$filename =  $path_parts["filename"];
    	$ext =  $path_parts["extension"];
        if ($this->validate()) {
        	$ext = $this->imageFile->extension;
            if (!is_dir($path)) 
                if (!mkdir($path,0777,TRUE))
                    return false;
            $sres = $this->imageFile->saveAs( $path.DIRECTORY_SEPARATOR.$filename.'.'.$ext);
            if ($sres)		// если удалось записать файл
				Yii::$app->db->createCommand("UPDATE powermeterdata SET mdatafile = '{$ext}' WHERE id=".$recordid)->execute();
        }
    }

    // Сохраняем показания с картинкой (если она есть)
    // MeterData - цифра показаний
    // MeterPhoto - файл картинки (объект null|yii\web\UploadedFile, загруженый с помощью getInstanceByName() )
    public function SaveReading($MeterDateTime, $MeterData, $MeterPhoto)
    {
		$oprights = Tickets::getUserOpRights();
		if( !empty($oprights) ) {
			$mid = $this->MeterId;
			$date = empty($MeterDateTime)?date("Y-m-d H:i:s"):$MeterDateTime;
			$who = $oprights['id'];
            $this->imageFile = $MeterPhoto;
            $obis = '1.8.0';
           	$rid = $this->InsertReading($mid, $who, $date, $obis, $MeterData, '1', NULL, NULL);
           	if (!empty($rid)){
           		if ($this->validate())				// проверяем картинку (точнее исходное название файла с картинкой)
           			$this->AddReadingPhoto( $rid );
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
    	$res = Yii::getAlias('@AppDataStore').DIRECTORY_SEPARATOR.Meter::READINGSPATH.DIRECTORY_SEPARATOR.'M'.$this->MeterId.DIRECTORY_SEPARATOR;
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

    // Выдать список типов имеющихся счетчиков
    public static function GetMeterTypesOptionsList()
    {
    	$res = "";
	   	$sqltext = "SELECT DISTINCT metermodel, '' as noop  FROM powermeter order by metermodel;";
	   	$models = ArrayHelper::map(Yii::$app->db->createCommand($sqltext)->queryAll(),'metermodel','noop');
	   //	if (!empty())

    	return $models;
    }

    public function SavePassport($data)
    {
    	$MeeterId = 0;
    	if (!empty($data)){
			$fields = [
				'metermodel' => $data['metermodel'],
				'meterserialno' => $data['meterserialno'],
				'meterdigits' => $data['meterdigits'],
				'meterphases' => $data['meterphases'],
				'metecalibrationinterval' => $data['metecalibrationinterval'],
				'metercurrent' => $data['metercurrent'],
				'metermaxcurrent' => $data['metermaxcurrent'],
				'metervoltage' => $data['metervoltage'],
				'metercomno' => $data['metercomno'],
				'metersysno' => $data['metersysno'],
				'meterimei' => empty($data['meterimei'])?NULL:$data['meterimei'],
				'meterphone' => empty($data['meterphone'])?NULL:$data['meterphone'],
				'meterip' => empty($data['meterip'])?NULL:$data['meterip'],
				'meterinventoryno' => empty($data['meterinventoryno'])?NULL:$data['meterinventoryno'],
				'meteraccno' => empty($data['meteraccno'])?NULL:$data['meteraccno'],
				'meteraccname' => empty($data['meteraccname'])?NULL:$data['meteraccname'],
				'meterowner' => empty($data['meterowner'])?NULL:$data['meterowner'],
				'meterdescr' => empty($data['meterdescr'])?NULL:$data['meterdescr'],
				'meterloaddescr' => empty($data['meterloaddescr'])?NULL:$data['meterloaddescr'],
				'meterporchno' => empty($data['meterporchno'])?NULL:$data['meterporchno'],
				'meterfacility_id' => $data['meterfacility_id'],
			];
    		if (empty($data['MeterId'])){
    			// Создаем новую запсь
				Yii::$app->db->createCommand()->insert('powermeter',$fields)->execute();    
            	$MeeterId = Yii::$app->db->getLastInsertID();
    		}else{
    			// Редактируем имеющеюся запись
	    		$MeeterId = $data['MeterId'];
				Yii::$app->db->createCommand()->update('powermeter',$fields, ['id'=>$MeeterId] )->execute();
    		}

    	}
    	return $MeeterId;
    }
}
