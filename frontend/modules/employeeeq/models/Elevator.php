<?php

namespace frontend\modules\employeeeq\models;


use Yii;
use frontend\modules\employeeeq\models\District;
use frontend\modules\employeeeq\models\Street;
use frontend\modules\employeeeq\models\Facility;
use frontend\modules\employeeeq\models\Employee;
use frontend\modules\employeeeq\models\Division;
use yii\helpers\Url;

/**
 * This is the model class for table "Elevator".
 *
 * @property int $id
 * @property int $elremoteid
 * @property int $eldevicetype
 * @property string $elserialno
 * @property string $elmodel
 * @property string $eldate
 * @property int $elload
 * @property string $elspeed
 * @property int $elstops
 * @property string $eldoortype
 * @property string $eltype
 * @property int $elporchno
 * @property string $elporchpos
 * @property string $elinventoryno
 * @property string $elregyear
 * @property int $elrtu_id
 * @property int $elfacility_id
 * @property int $eldivision_id
 * @property int $elperson_id
 */
class Elevator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Elevator';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['elremoteid', 'eldevicetype', 'elload', 'elstops', 'elporchno', 'elrtu_id', 'elfacility_id', 'eldivision_id', 'elperson_id'], 'integer'],
            [['eldate'], 'safe'],
            [['elspeed'], 'number'],
            [['elfacility_id'], 'required'],
            [['elserialno', 'elmodel', 'elinventoryno'], 'string', 'max' => 50],
            [['eldoortype', 'eltype', 'elporchpos'], 'string', 'max' => 20],
            [['elregyear'], 'string', 'max' => 4],
            //[['elrtu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rtu::className(), 'targetAttribute' => ['elrtu_id' => 'id']],
            //[['elfacility_id'], 'exist', 'skipOnError' => true, 'targetClass' => Facility::className(), 'targetAttribute' => ['elfacility_id' => 'id']],
            [['eldivision_id'], 'exist', 'skipOnError' => true, 'targetClass' => Division::className(), 'targetAttribute' => ['eldivision_id' => 'id']],
            [['elperson_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['elperson_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'eltype' => 'Наименование оборудования',//'Eltype'
            'elporchno' => 'Подъезд',//'Elporchno',
            'elinventoryno' => 'Инвентарный номер',//'Elinventoryno',
            'elperson_id' => 'Электромеханик',//'Elperson ID',
            'district' => 'Район',
            'elfacility_id' => 'Обьект',//'Elfacility ID',//'Обьект',
            'id' => 'ID',
            'elremoteid' => 'Elremoteid',//номер смежной системы
            'eldevicetype' => 'Eldevicetype',//1-Лифт 10-ВДЭС 20-Домофоны
            'elserialno' => 'Elserialno',
            'elmodel' => 'Elmodel',
            'eldate' => 'Eldate',
            'elload' => 'Elload',
            'elspeed' => 'Elspeed',
            'elstops' => 'Elstops',
            'eldoortype' => 'Eldoortype',
            'elporchpos' => 'Elporchpos',
            'elregyear' => 'Elregyear',
            'elrtu_id' => 'Elrtu ID',
            'eldivision_id' => 'Подразделение'//'Eldivision ID',
            
        ];
    }

    public function getGh()
    {
        return 'gh';
    }   
    public function getElfacility()
    {
        return $this->hasOne(Facility::className(), ['id' => 'elfacility_id']);
    }                                                         

    public function getEldivision()//Связь Подразделениями
    {
        return $this->hasOne(Division::className(), ['id' => 'eldivision_id']);
    }

    public function getElperson()
    {
        return $this->hasOne(Employee::className(), ['id' => 'elperson_id']);
    }    

        public function getEldivisionname()//Название Подразделения
    {
        return $this->eldivision->divisionname;
    }
    
    public function getDistrictname()//район
    {
        return $this->elfacility->fadistrict->districtname;
    }

    public function getDistrict()//район
    {
        return $this->elfacility->fadistrict_id;
    }
    
    /* public function getDistrict()//район
    {
        return ($this->elfacility->fadistrict->districtname);
    }*/
    public function getStreetname()
    {
        return ($this->elfacility->fastreet->streettype .' '.  $this->elfacility->fastreet->streetnameru);   
    }
    public function getfaaddressno()
    {
        return ($this->elfacility->faaddressno);   
    }
    public function getElpersonname()
    {
        return $this->elperson->fullname;
    }
    public function getUrlelp() 
    {
        return (Url::toRoute(['employee/emechanic','ElevatorSearch[elperson_id]'=>$this->elperson_id,'id'=>$this->elperson_id]) );
    }   
    public function getUrldivision1() 
    {
        return (Url::toRoute(['elevator/emechanic','id'=>$this->eldivision_id]) );
    }  
    public function getEltypel ()//тип Лифта (правый/левый груз/пас)
    {
        $eldt=$this->eldevicetype;//1-Лифт 10-ВДЭС 20-Домофоны
        if ($eldt==1) 
        {
            $eldt='Лифт'; 
            $elt=$this->eltype;
            $eltrl=$this->elporchpos;
            if ($elt=='вант-пас') { $elt='гр'; }
                //elseif ($elt=='' and $eltrl=='') { return  '';}
            return $eldt.' '.$eltrl.' '.$elt;
        } 
            elseif ($eldt==10) {$eldt='Электрощитовая'; }
                elseif ($eldt==20) {$eldt='Домофоны'; }
                    else {$eldt=''; }
        return $eldt.' '.$eltrl.' '.$elt;
    }
    public function getUrlinventoryno()//паспорт оборудования
    {
        return (Url::toRoute(['//facilityeq/elevator/view','id'=>$this->id]) );
    }
}