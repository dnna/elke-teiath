<?php
class Timesheets_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initTimesheetsHelpers() {
        $front = Zend_Controller_Front::getInstance();
        $moduledir = $front->getModuleDirectory(strtolower($this->getModuleName()));
        Zend_Controller_Action_HelperBroker::addPath($moduledir.'/controllers/helpers', 'Timesheets_Action_Helper');
    }
}
?>