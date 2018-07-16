<?php
namespace frontend\modules\meter;

class MeterModule extends \yii\base\Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->i18n->translations['meter*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath' => '@frontend/modules/meter/messages',
            'fileMap' => [
            	'meter' => 'meter.php',
            ],
        ];
    }
}
