<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_EkprothesmaController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Εκπρόθεσμα Παραδοτέα";
    }

    public function indexAction() {
        $this->view->paradotea = Zend_Registry::get('entityManager')
                                ->getRepository('Erga_Model_SubItems_Deliverable')
                                ->findOverdueDeliverables();
    }
}

?>