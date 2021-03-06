<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetRenderPath extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getRenderPath($file) {
        return strtolower(Zend_Controller_Front::getInstance()->getRequest()->getControllerName()).'/'.$file;
    }
}
?>