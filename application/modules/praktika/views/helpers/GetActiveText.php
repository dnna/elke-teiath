<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetActiveText extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getActiveText(Praktika_Model_CommitteeBase $committee) {
        if($committee->get_active()) {
            return 'Ναι';
        } else {
            return 'Όχι';
        }
    }
}
?>