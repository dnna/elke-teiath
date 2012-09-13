<?php
class Help_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initHelpRoute() {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        // Specifying the help route
        $route = new Zend_Controller_Router_Route('help/*', array('module' => 'help', 'controller' => 'catchall', 'action' => 'index'));
        $router->addRoute('help', $route);
    }
}
?>