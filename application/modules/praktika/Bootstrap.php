<?php
class Praktika_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initPraktikaHelpers() {
        $front = Zend_Controller_Front::getInstance();
        $moduledir = $front->getModuleDirectory(strtolower($this->getModuleName()));
        Zend_Controller_Action_HelperBroker::addPath($moduledir.'/controllers/helpers', 'Praktika_Action_Helper');
        $front->registerPlugin(new Praktika_Plugin_PopulateNavigationPlugin($this->getModuleName()));
    }
}
?>