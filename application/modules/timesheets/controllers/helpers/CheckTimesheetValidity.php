<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Action_Helper_CheckTimesheetValidity extends Zend_Controller_Action_Helper_Abstract
{
    public function direct(Timesheets_Model_Timesheet $timesheet) {
        $sumhours = array();
        $deliverables = array();
        foreach($timesheet->get_activities() as $curActivity) {
            $deliverable = $curActivity->get_deliverable();
            if(!isset($deliverables[$deliverable->get_recordid()])) {
                $deliverables[$deliverable->get_recordid()] = $deliverable;
            }
            if(!isset($sumhours[$deliverable->get_recordid()])) {
                $sumhours[$deliverable->get_recordid()] = 0;
            }
            $sumhours[$deliverable->get_recordid()] = $sumhours[$deliverable->get_recordid()] + $curActivity->get_duration();
        }
        $sumpayment = 0;
        foreach($sumhours as $deliverableid => $hours) {
            $rate = $deliverables[$deliverableid]->get_authorFromEmployee($timesheet->get_employee())->get_rateAsFloat();
            $payment = $rate*$hours;
            $sumpayment = $sumpayment + $payment;
            // 1) rate*hours < deliverable->amount
            if($payment > $deliverables[$deliverableid]->get_amountAsFloat()) {
                throw new Exception('Το πληρωτέο ποσό με βάση τις εισαγόμενες ώρες ('.$payment.'€) υπερβαίνει τον προϋπολογισμό του παραδοτέου ('.$deliverables[$deliverableid]->get_amount().'€).');
            }
        }
        // 2) rate*hours < employee->amount (να μην ξεπερνάει το ποσό της σύμβασης του απασχολούμενου)
        if($sumpayment > $timesheet->get_employee()->get_amountAsFloat()) {
            throw new Exception('Το πληρωτέο ποσό με βάση τις εισαγόμενες ώρες ('.$sumpayment.') υπερβαίνει τη σύμβαση του απασχολούμενου ('.$timesheet->get_employee()->get_amount().').');
        }
        // 3) Να μη επιτρέπεται να γίνονται overlap οι ώρες ενός απασχολούμενου
        function overlaps(Timesheets_Model_Activity $activity1, Timesheets_Model_Activity $activity2) {
            return !(
                ($activity1->get_startAsDate() < $activity2->get_startAsDate() && $activity1->get_endAsDate() <= $activity2->get_startAsDate())
                ||
                ($activity1->get_startAsDate() >= $activity2->get_endAsDate() && $activity1->get_endAsDate() > $activity2->get_endAsDate())
            );
        }
        for($i = 1; $i < $timesheet->get_monthAsDate()->format('t'); $i++) {
            $activities = $timesheet->get_activitiesForDay($i);
            foreach($activities as $activity1) {
                foreach($activities as $activity2) {
                    if($activity1 == $activity2) {
                        continue;
                    }
                    if(overlaps($activity1, $activity2)) {
                        throw new Exception('Η ημέρα '.$activity1->get_day().' περιέχει αλληλοεπικαλυπτόμενες ώρες.');
                    }
                }
            }
        }

        // Ελέγχουμε ότι το φύλλο θα μπορέσει να εγκριθεί
        $this->getActionController()->getHelper('CheckApprovalValidity')->direct($timesheet);
    }
}
?>