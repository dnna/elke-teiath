<?php
class Application_Action_Helper_GetBinaryDataSize extends Zend_Controller_Action_Helper_Url
{
    public function direct($data)
    {
        $has_mbstring = extension_loaded('mbstring') ||@dl(PHP_SHLIB_PREFIX.'mbstring.'.PHP_SHLIB_SUFFIX);
        $has_mb_shadow = (int) ini_get('mbstring.func_overload');

        if ($has_mbstring && ($has_mb_shadow & 2) ) {
           return mb_strlen($data,'latin1');
        } else {
           return strlen($data);
        }
    }
}
?>