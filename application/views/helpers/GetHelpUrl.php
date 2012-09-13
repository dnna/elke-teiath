<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetHelpUrl extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getHelpUrl($request) {
        if($request->getControllerName() === 'error') {
            return '';
        }
        $url = $this->view->baseUrl().'/help'.$this->view->url($request->getUserParams(), 'default', true);
        if($request->getModuleName() === 'help') {
            $url = $this->view->baseUrl().'/help/';
        }
        return $url;
    }
}
?>