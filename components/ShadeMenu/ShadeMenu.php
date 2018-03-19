<?php

namespace components\ShadeMenu;
 
use yii\base\Widget;
use yii\helpers\Html;

class ShadeMenu extends Widget
{
    public $caption;
    public $items;
    public $options;

    private $_HTMLstr;
    public function init() {
        parent::init();
        $this->_HTMLstr = "";
    }

    public function run() {
        foreach ($this->items as $value) {
            $this->AddItem($value['caption'], $value['items'], []);
        }
        return $this->_HTMLstr;
    }

    public function registerAssets()
    {
        $view = $this->getView();
        //ShadeMenuAsset::register($view);
    }

    public function AddItem($caption, $item)
    {
        if (is_array($item)) {
            $this->_HTMLstr .= "<ul>\n";
            foreach ($item as $value) {
                $this->AddItem($value['caption'], $value['items'], []);
            }
            $this->_HTMLstr .= "</ul>\n";
        }else{
            $this->_HTMLstr .= "<li>".Html::a($caption, [$item], [])."</li>\n";
        }
    }
}
