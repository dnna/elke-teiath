<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_View_Helper_GetApprovalIcon extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getApprovalIcon(Timesheets_Model_Timesheet $timesheet) {
        if($timesheet->get_approved() == Timesheets_Model_Timesheet::PENDING) {
            return '<img title="Εκκρεμεί" class="timesheetpending" src="'.$this->view->baseUrl('images/pending.gif').'" style="display:inline">';
        } else if($timesheet->get_approved() == Timesheets_Model_Timesheet::APPROVED) {
            return '<img title="Εγκρίθηκε" class="timesheetapproved" src="'.$this->view->baseUrl('images/tick.png').' " style="display:inline">';
        } else {
            return '<img title="Απορρίφθηκε" class="timesheetrejected" src="'.$this->view->baseUrl('images/redx.png').' " style="display:inline">';
        }
    }
}
?>