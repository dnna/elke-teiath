<?php
/**
 * @author Joshua Thijssen
 * @link http://www.enrise.com/2011/01/rest-style-context-switching-part-2/
 */
class Api_Plugin_PerformApiConversions extends Zend_Controller_Plugin_Abstract
{
    public function __construct($moduleName) {
        $this->__moduleName = strtolower($moduleName);
    }

    public function routeShutdown (Zend_Controller_Request_Abstract $request)
    {
        if ($this->__moduleName != $request->getModuleName() || 'subforms' === $request->getControllerName()) {
            // If not in this module, return early
            return;
        }
        Zend_Registry::set('performApiConversions', true);
        Zend_Registry::set('Zend_Locale', new Zend_Locale('en_US'));
    }
}
?>