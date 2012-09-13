<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetCurrentUrl extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getCurrentUrl() {        
        return (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
}
?>