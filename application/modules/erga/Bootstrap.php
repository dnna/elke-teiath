<?php
class Erga_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initProjectNavigation() {
        $moduledir = Zend_Controller_Front::getInstance()->getModuleDirectory(strtolower($this->getModuleName()));
        $navigationConfig = new Zend_Config_Xml($moduledir.'/configs/projectnavigation.xml');
        Zend_Registry::set('projectnavigation', new Zend_Navigation($navigationConfig));
    }
}
?>