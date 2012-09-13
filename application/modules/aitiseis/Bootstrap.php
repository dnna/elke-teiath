<?php
class Aitiseis_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initAitiseisHelpers() {
        $front = Zend_Controller_Front::getInstance();
        $moduledir = $front->getModuleDirectory(strtolower($this->getModuleName()));
        Zend_Controller_Action_HelperBroker::addPath($moduledir.'/controllers/helpers', 'Aitiseis_Action_Helper');
        //$front->registerPlugin(new Aitiseis_Plugin_PopulateNavigationPlugin($this->getModuleName()));
    }
}
?>