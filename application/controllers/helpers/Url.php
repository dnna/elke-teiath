<?php
class Application_Action_Helper_Url extends Zend_Controller_Action_Helper_Url
{
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $fragment = '';
        if(isset($urlOptions['_fragment'])) {
            $fragment = '#' . $urlOptions['_fragment'];
            unset($urlOptions['_fragment']);
        }

        return parent::url($urlOptions, $name, $reset, $encode) . $fragment;
    }
}
?>