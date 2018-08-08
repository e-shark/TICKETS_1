<?php

namespace frontend\modules\employeeeq\models;

use Yii;

/**
 * This is the model class for table "district".
 *
 * @property int $id
 * @property string $districtname
 * @property string $districtnameeng
 * @property string $districtcode
 * @property int $districtlistno
 * @property int $districtlocality_id
 *
 * @property Locality $districtlocality
 * @property Facility[] $facilities
 */
class District extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

  //  public static function getDb()
   // {
  //      return Yii::$app->get('db');
    //}
    


    public static function tableName()
    {
        return 'district';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['districtlistno', 'districtlocality_id'], 'integer'],
            [['districtlocality_id'], 'required'],
            [['districtname', 'districtnameeng'], 'string', 'max' => 80],
            [['districtcode'], 'string', 'max' => 10],
            [['districtlocality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locality::className(), 'targetAttribute' => ['districtlocality_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'districtname' => 'Districtname',
            'districtnameeng' => 'Districtnameeng',
            'districtcode' => 'Districtcode',
            'districtlistno' => 'Districtlistno',
            'districtlocality_id' => 'Districtlocality ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistrictlocality()
    {
        return $this->hasOne(Locality::className(), ['id' => 'districtlocality_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacilities()
    {
        return $this->hasMany(Facility::className(), ['fadistrict_id' => 'id']);
    }
}
