<?php

namespace frontend\modules\employeeeq\models;
use frontend\modules\employeeeq\models\District;

use Yii;

/**
 * This is the model class for table "street".
 *
 * @property int $id
 * @property string $streetdistrict
 * @property string $streetname
 * @property string $streetnameru
 * @property string $streetnameeng
 * @property string $streettype
 * @property int $streetcode
 * @property string $streetzip
 * @property int $streetlocality_id
 *
 * @property Facility[] $facilities
 * @property Locality $streetlocality
 */
class Street extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
 
/*
    public static function getDb()
    {
        return Yii::$app->get('db1');
    }
*/    
   
    public static function tableName()
    {
        return 'street';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['streetcode', 'streetlocality_id'], 'integer'],
            [['streetlocality_id'], 'required'],
            [['streetdistrict', 'streetname', 'streetnameru', 'streetnameeng'], 'string', 'max' => 80],
            [['streettype'], 'string', 'max' => 10],
            [['streetzip'], 'string', 'max' => 200],
            array(['streetname', 'streetnameru'], 'match', 'pattern' => '/^[0-9А-яА-яЄєІЇіїЁё_\s\']+$/u', 'message' => 'Поле может содержать только буквы кириллицы'),
            array('streetnameeng', 'match', 'pattern' => '/^[0-9A-z\s]+$/', 'message' => 'Поле может содержать только буквы латинского алфавита'),
            [['streetlocality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Locality::className(), 'targetAttribute' => ['streetlocality_id' => 'id']],
            [['streetdistrict', 'streetname', 'streetnameru','streetcode'], 'unique','targetAttribute' => ['streetdistrict', 'streetname', 'streetnameru','streetcode'],'skipOnEmpty' => true, 'skipOnError' => true, 'message'=>'Запись с таким именем уже существует.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'streetdistrict' => 'Streetdistrict',
            'streetname' => 'Streetname',
            'streetnameru' => 'Streetnameru',
            'streetnameeng' => 'Streetnameeng',
            'streettype' => 'Streettype',
            'streetcode' => 'Streetcode',
            'streetzip' => 'Streetzip',
            'streetlocality_id' => 'Streetlocality ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStreetlocality()
    {
        return $this->hasOne(Locality::className(), ['id' => 'streetlocality_id']);
    }

}
