<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_IncludeFile extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function includeFile($file) {
        return $this->view->render(str_replace("_", "/", $this->view->getRenderPath($file)));
    }
}
?>