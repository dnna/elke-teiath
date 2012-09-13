<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetContractorStageText extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getContractorStageText(Erga_Model_SubItems_SubProjectContractor $contract) {
        $stage = $contract->get_contractstage();
        if($stage === '0') {
            return 'Δεν έχει γίνει προσωρινή παραλαβή';
        } else if($stage === '1') {
            return 'Προσωρινή Παραλαβή '.$contract->get_provisionalacceptancedate();
        } else if($stage === '2') {
            return 'Οριστική Παραλαβή '.$contract->get_finalacceptancedate();
        } else if($stage === '3') {
            return 'Αποπληρωμή '.$contract->get_repaymentdate();
        }
    }
}
?>