<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_GetProjectId extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param int $approved 0. Δεν έχει αποφασιστεί, 1. Εκγρίθηκε, 2. Απορρίφθηκε
     * @param array $entries Οι αιτήσεις
     */
    public function getProjectId() {
        return $this->view->getProject()->get_projectid();
    }
}
?>