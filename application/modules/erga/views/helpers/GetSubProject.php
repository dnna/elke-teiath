<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_GetSubProject extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param int $approved 0. Δεν έχει αποφασιστεί, 1. Εκγρίθηκε, 2. Απορρίφθηκε
     * @param array $entries Οι αιτήσεις
     */
    public function getSubProject() {
        if(isset($this->view->subproject)) {
            $subproject = $this->view->subproject;
        } else if(isset($this->view->workpackage)) {
            $subproject = $this->view->workpackage->get_subproject();
        } else if(isset($this->view->deliverable)) {
            $subproject = $this->view->deliverable->get_workpackage()->get_subproject();
        } else if(isset($this->view->employee)) {
            $project = $this->view->employee->get_subproject();
        } else {
            throw new Exception('Δεν μπόρεσε να ανακτηθεί το subproject.');
        }
        return $subproject;
    }
}
?>