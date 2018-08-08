<?php

namespace frontend\modules\employeeeq\models;

use frontend\modules\employeeeq\models\District;
use frontend\modules\employeeeq\models\Street;
use yii\helpers\ArrayHelper;


use Yii;

/**
 * This is the model class for table "facility".
 *
 * @property int $id
 * @property string $facode
 * @property string $facodesvc
 * @property string $fainventoryno
 * @property string $faaddressno
 * @property string $fabuildingno
 * @property string $fasectionno
 * @property int $fastoreysnum
 * @property int $faporchesnum
 * @property string $fabseries
 * @property string $fatype
 * @property string $fadescription
 * @property string $fadate
 * @property string $facomdate
 * @property string $fadecomdate
 * @property string $faserviceno
 * @property double $falatitude
 * @property double $falongitude
 * @property int $fastreet_id
 * @property int $fadistrict_id
 *
 * @property Elevator[] $elevators
 * @property Street $fastreet
 * @property District $fadistrict
 * @property Rtu[] $rtus
 */
class Facility extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $myattr;
    public $fastreettype;

 /*   public static function getDb()
    {
        return Yii::$app->get('db1');
    }
    *

    public static function tableName()
    {
        return 'facility';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fastoreysnum', 'faporchesnum', 'fastreet_id', 'fadistrict_id'], 'integer'],
            [['fadate', 'facomdate', 'fadecomdate', 'myattr','fastreettype'], 'safe'],
            [['falatitude', 'falongitude'], 'number'],
            [['facode', 'facodesvc', 'faserviceno'], 'string', 'max' => 10],
            [['fainventoryno', 'faaddressno', 'fabuildingno', 'fasectionno', 'fabseries'], 'string', 'max' => 20],
            [['fatype', 'fadescription'], 'string', 'max' => 100],
            [['fastreet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Street::className(), 'targetAttribute' => ['fastreet_id' => 'id']],
            [['fadistrict_id'], 'exist', 'skipOnError' => true, 'targetClass' => District::className(), 'targetAttribute' => ['fadistrict_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'facode' => 'Facode',
            'facodesvc' => 'Facodesvc',
            'fainventoryno' => 'Fainventoryno',
            'faaddressno' => 'Faaddressno',
            //'myattr' => 'myattr',
            'fastreettype' => 'Fastreettype',
            'fabuildingno' => 'Fabuildingno',
            'fasectionno' => 'Fasectionno',
            'fastoreysnum' => 'Fastoreysnum',
            'faporchesnum' => 'Faporchesnum',
            'fabseries' => 'Fabseries',
            'fatype' => 'Fatype',
            'fadescription' => 'Fadescription',
            'fadate' => 'Fadate',
            'facomdate' => 'Facomdate',
            'fadecomdate' => 'Fadecomdate',
            'faserviceno' => 'Faserviceno',
            'falatitude' => 'Falatitude',
            'falongitude' => 'Falongitude',
            'fastreet_id' => 'Fastreet ID',
            'fadistrict_id' => 'Fadistrict ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getElevators()
    {
        return $this->hasMany(Elevator::className(), ['elfacility_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFastreet()
    {
        return $this->hasOne(Street::className(), ['id' => 'fastreet_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getFadistrict()
    {
        return $this->hasOne(District::className(), ['id' => 'fadistrict_id']);
    }


    public function getLocality()
    {
        return $this->hasOne(Locality::className(), ['id' => 'fadistrict_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRtus()
    {
        return $this->hasMany(Rtu::className(), ['rtufacility_id' => 'id']);
    }


    function getMyDistr()
    {
            $distrname = mb_convert_case($this->fadistrict->districtname, MB_CASE_TITLE, "UTF-8"). " р-н";
            return $distrname;
    }
        
    
    function getMyName()
    {  
        $distrname = mb_convert_case($this->fadistrict->districtname, MB_CASE_TITLE, "UTF-8"). " р-н, ";
        if ($this->fasectionno)
        {
            $sectionnu= ", секция " . $this->fasectionno;
        }
        else
        {
            $sectionnu= " ";
        }

        $streetnm =  $distrname . $this->fastreet->streettype.' ' . $this->fastreet->streetnameru . ', ' . $this->fatype . ' № ' . $this->fabuildingno . ' ' . $sectionnu;

        return $streetnm;
    }

    
      function getMyFasName()
    {  
      
        if ($this->fasectionno)
        {
            $sectionnu= ", секция " . $this->fasectionno;
        }
        else
        {
            $sectionnu= " ";
        }

        $streetname =  $this->fastreet->streettype .' ' . $this->fastreet->streetnameru . ', ' . 'дом № ' . $this->fabuildingno . ' ' . $sectionnu;

        return $streetname;
         
    } 
} 
