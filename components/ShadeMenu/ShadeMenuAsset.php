<?php
namespace components\ShadeMenu;

use yii\web\AssetBundle;

class ShadeMenuAsset extends AssetBundle
{
    public $sourcePath = '@app/../components/ShadeMenu/';
    public $css = [
        'css/shade-menu.css'
    ];
    
    public $js = [
    ];
    public $depends = [
    ];
}

