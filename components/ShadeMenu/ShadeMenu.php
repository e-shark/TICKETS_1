<?php

namespace eshark\ShadeMenu;
 
use Yii;
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
        $this->registerAssets();
    }

    public function run() {
        $this->_HTMLstr .= "<input type='checkbox' id='rep-nav-toggle' hidden> \n";
        $this->_HTMLstr .= "<div class='rep-nav'> \n";
        $this->_HTMLstr .= "<label for='rep-nav-toggle' class='rep-nav-toggle' onclick=''></label> \n";
        $this->_HTMLstr .= '<h2 class="logo">'.$this->caption."</h2> \n";
        $this->_HTMLstr .= "<ul> \n";
        foreach ($this->items as $value) {
            $this->AddItem($value);
        }

        $this->_HTMLstr .= "</ul> \n";
        $this->_HTMLstr .= "</div> \n";        
        return $this->_HTMLstr;
    }

    public function registerAssets()
    {
        $view = $this->getView();
        ShadeMenuAsset::register($view);
    }

    public function AddItem($item)
    {
        $caption = $item['caption'];
        $href =  $item['href'];
        $items =  $item['items'];
        $this->_HTMLstr .= "<li>";
        if ((!empty($items)) && (is_array($items))) {
            if ($this->IfRoutePresent($items, Yii::$app->controller->route))
                $checked = "checked";
            else
                $checked = "";
            $this->_HTMLstr .= "<input type='checkbox' id='group-1' $checked hidden> \n";
            $this->_HTMLstr .= '<label for="group-1">'.$caption."<i></i></label>\n";
            $this->_HTMLstr .= "<ul>\n";
            foreach ($items as $value) {
                $this->AddItem($value);
            }
            $this->_HTMLstr .= "</ul>\n";
        }else{
            if (empty($href)) $href= "#1";
            $this->_HTMLstr .= Html::a($caption, [$href], []);
        }
        $this->_HTMLstr .= "</li>\n";
    }

    /*  Определяет, есть ли указаная ссылка в списке пунктов меню
        (не реализует вложения) */
    public function IfRoutePresent($items, $route)
    {
        $res = false;
        foreach ($items as $value) {
            if (!empty($value['href']))
                if ($route == $value['href']) 
                    $res = true;
        }
        return $res;
    }

}
