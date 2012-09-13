<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_FlexboxDependencies extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function flexboxDependencies() {
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('media/css/jquery.flexbox.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/jquery.flexbox.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/comboselect.js', 'text/javascript'));
    }
}
?>