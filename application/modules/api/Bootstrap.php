<?php
class Api_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initRestRoute() {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        // Specifying the "api" module only as RESTful:
        $restRoute = new Dnna_Plugin_ApiRoute($front, array(), array(
            'api',
        ));
        $restRoute->setCache(Zend_Registry::get('cache'));
        $router->addRoute('rest', $restRoute);
    }

    protected function _initApiHelpers() {
        $front = Zend_Controller_Front::getInstance();
        $moduledir = $front->getModuleDirectory(strtolower($this->getModuleName()));
        include_once($moduledir.'/controllers/IndexController.php');
        $front->registerPlugin(new Api_Plugin_AclPlugin($this->getModuleName()));
        $front->registerPlugin(new Api_Plugin_AcceptHandler($this->getModuleName()));
        $front->registerPlugin(new Api_Plugin_PerformApiConversions($this->getModuleName()));
        $front->registerPlugin(new Api_Plugin_ErrorControllerSwitcher($this->getModuleName()));
        Zend_Controller_Action_HelperBroker::addPath($moduledir.'/controllers/helpers', 'Api_Action_Helper');
    }
}
?>