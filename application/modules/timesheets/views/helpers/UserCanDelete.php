<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_UserCanDelete extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function userCanDelete(Timesheets_Model_Timesheet $timesheet, $enableallagi = false) {
        // Τα φύλλα στο εκπαιδευτικό έργο επιτρέπεται πάντα να διαγραφούν
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        if($timesheet->get_project() != null && $timesheet->get_project()->get_projectid() == $options['project']['educational']) {
                return true;
        }
        if($timesheet->get_approved() == Timesheets_Model_Timesheet::PENDING) {
            return true;
        } else if($timesheet->get_approved() == Timesheets_Model_Timesheet::REJECTED) {
            if($enableallagi == true) {
                return true;
            }
        }
        return false;
    }
}
?>