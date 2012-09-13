<?php
/**
 * @author Tomáš Fejfar
 * @link http://stackoverflow.com/questions/2720037/zend-framework-module-based-error-handling
 */
class Api_Plugin_ErrorControllerSwitcher extends Zend_Controller_Plugin_Abstract
{
    public function __construct($moduleName) {
        $this->__moduleName = strtolower($moduleName);
    }

    public function routeShutdown (Zend_Controller_Request_Abstract $request) {
        if ($this->__moduleName != $request->getModuleName()) {
            // If not in this module, return early
            return;
        }
        $front = Zend_Controller_Front::getInstance();
        if (!($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceof Zend_Controller_Plugin_ErrorHandler)) {
            return;
        }
        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');
        $testRequest = new Zend_Controller_Request_Http();
        $testRequest->setModuleName($request->getModuleName())
                    ->setControllerName($error->getErrorHandlerController())
                    ->setActionName($error->getErrorHandlerAction());
        if ($front->getDispatcher()->isDispatchable($testRequest)) {
            $error->setErrorHandlerModule($request->getModuleName());
        }
    }
}