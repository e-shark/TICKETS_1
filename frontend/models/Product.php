<?php

namespace frontend\models;
use zxbodya\yii2\galleryManager\GalleryBehavior;
use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 * @property integer $population
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gallery_image';
    }

 //   public function rules()
 //   {
 //       return [[['name', 'description'], 'string']];
 //   }


    public function attributeLabels()
    {
        return ['id' => 'ID', 'name' => 'Name', 'description' => 'Description'];
    }

    public function behaviors()
    {
        return [
             'galleryBehavior' => [
                 'class' => GalleryBehavior::className(),
                 'type' => 'product',
                 'extension' => 'jpg',
                 'directory' => Yii::getAlias('@webroot') . '/Documents/',
                 'url' => Yii::getAlias('@web') . '/Documents/',
                 'versions' => [
                     'small' => function ($img) {
                         /** @var \Imagine\Image\ImageInterface $img */
                         return $img
                             ->copy()
                             ->thumbnail(new \Imagine\Image\Box(200, 200));
                     },
                     'medium' => function ($img) {
                         /** @var Imagine\Image\ImageInterface $img */
                         $dstSize = $img->getSize();
                         $maxWidth = 800;
                         if ($dstSize->getWidth() > $maxWidth) {
                             $dstSize = $dstSize->widen($maxWidth);
                         }
                         return $img
                             ->copy()
                             ->resize($dstSize);
                     },
                 ]
             ]
        ];
    }


} 

