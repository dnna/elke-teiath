<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetModuleName extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getModuleName() {
        return Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
    }
}
?>