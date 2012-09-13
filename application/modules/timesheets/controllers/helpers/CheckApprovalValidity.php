<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Action_Helper_CheckApprovalValidity extends Zend_Controller_Action_Helper_Abstract
{
    public function direct(Timesheets_Model_Timesheet $timesheet) {
        // 1) Να μην επιτρέπεται να ξεπεραστούν οι maxhours του απασχολούμενου μετά από την εισαγωγή του νέου φύλλου
        $currenthours = $workinghours = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHours(array(
            'afm'   =>  $timesheet->get_employee()->get_employee()->get_afm(),
            'year'  =>  $timesheet->get_year(),
        ));
        $newhours = $currenthours[0]['hours'] + $timesheet->getTotalHours();
        $allowedhours = (int)$timesheet->get_employee()->get_employee()->get_maxhours();
        if($newhours > $allowedhours) {
            throw new Exception('Σφάλμα: Με την υποβολή/έγκριση του συγκεκριμένου φύλλου ο συνολικός αριθμός ωρών ('.$newhours.') θα ξεπερνάει το ετήσιο όριο για τον απασχολούμενο ('.$allowedhours.').');
        }
    }
}
?>