<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_GetProject extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    /**
     * @param int $approved 0. Δεν έχει αποφασιστεί, 1. Εκγρίθηκε, 2. Απορρίφθηκε
     * @param array $entries Οι αιτήσεις
     */
    public function getProject() {
        if(isset($this->view->project)) {
            $project = $this->view->project;
        } else if(isset($this->view->subproject)) {
            $project = $this->view->subproject->get_parentproject();
        } else if(isset($this->view->workpackage)) {
            $project = $this->view->workpackage->get_subproject()->get_parentproject();
        } else if(isset($this->view->deliverable)) {
            $project = $this->view->deliverable->get_workpackage()->get_subproject()->get_parentproject();
        } else if(isset($this->view->employee)) {
            if($this->view->employee->get_subproject() != null) {
                $project = $this->view->employee->get_subproject()->get_parentproject();
            } else if($this->view->employee->get_project() != null) {
                $project = $this->view->employee->get_project();
            }
        } else if(isset($this->view->contractor)) {
            $project = $this->view->contractor->get_subproject()->get_parentproject();
        } else {
            //throw new Exception('Δεν μπόρεσε να ανακτηθεί το project.');
            return null;
        }
        return $project;
    }
}
?>