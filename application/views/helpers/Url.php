<?php
class Application_View_Helper_Url extends Zend_View_Helper_Url
{    
    public function url(array $urlOptions  = array(), $name   = null, $reset = false, $encode = true) {
        $uri = parent::url($urlOptions, $name, $reset, $encode);
        if(!$reset && $_SERVER['QUERY_STRING'] != "") {
            return $uri.'?'.$_SERVER['QUERY_STRING'];
        } else {
            return $uri;
        }
    }
}
?>