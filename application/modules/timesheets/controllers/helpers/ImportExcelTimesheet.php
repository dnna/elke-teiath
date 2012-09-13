<?php

require_once APPLICATION_PATH . '/../library/PHPExcel/PHPExcel.php';

class Timesheets_Action_Helper_ImportExcelTimesheet extends Zend_Controller_Action_Helper_Abstract {
    const STARTROW = 12;

    protected function addTimesheetDetails(Timesheets_Model_Timesheet &$timesheet, $data) {
        $employee = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($data['recordid']);
        if(!isset($employee) || count($employee) <= 0) {
            return array('error' => true, 'errorRow' => 1, 'formElements' => array(), 'message' => 'Δεν βρέθηκε ο απασχολούμενος.');
        }
        $timesheet->set_employee($employee);

        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findProjects(array('mis' => $data['mis']));
        if(!isset($project) || count($project) <= 0) {
            return array('error' => true, 'errorRow' => 1, 'formElements' => array(), 'message' => 'Δεν βρέθηκε το έργο.');
        }
        $timesheet->set_project($project[0]);
        $timesheet->set_year($data['year']);
        $timesheet->set_month($data['month']);
    }

    protected function addActivities(PHPExcel_Worksheet $worksheet, Timesheets_Model_Timesheet &$timesheet, Erga_Model_SubItems_Deliverable $deliverable, $delcolumn) {
        $row = self::STARTROW + 1;
        for($i = 1; $i <= $timesheet->get_monthAsDate()->format('t'); $i++) {
            $data = array();
            $value = $worksheet->getCell($delcolumn.$row)->getCalculatedValue();
            if(strpos($value, '-') !== false) {
                $hours = explode('-', $value);
            }
            if(isset($hours) && is_array($hours) && $hours[0] != '') {
                $data['start'] = $hours[0];
                $data['end'] = $hours[1];
                $form = new Timesheets_Form_Activity();
                if($form->isValid($data)) {
                    if($data['end'] < $data['start']) {
                        throw new Exception('Κελί '.$delcolumn.$row.': Η ώρα έναρξης δεν μπορεί να είναι μεταγενέστερη της ώρας λήξης');
                    }
                    $activity = new Timesheets_Model_Activity();
                    $activity->set_timesheet($timesheet);
                    $activity->set_day($i);
                    $activity->set_start($data['start']);
                    $activity->set_end($data['end']);
                    $activity->set_deliverable($deliverable);
                    $timesheet->get_activities()->add($activity);
                } else {
                    throw new Exception('Μη-έγκυρα δεδομένα στο κελί '.$delcolumn.$row);
                }
            }
            unset($hours);
            $row++;
        }
    }

    function direct(Zend_Controller_Action $controller, $filepath) {
        // Read the template file
        $ext = substr(strrchr($filepath, '.'), 1);
        if ($ext === 'xlsx') {
            $inputFileType = 'Excel2007';
        } else {
            $inputFileType = 'Excel5';
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filepath);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        if($objWorksheet->getTitle() === 'ΜΦΠ') {
            $form = new Timesheets_Form_Timesheet();
            $data = array();
            $data['uniquecode'] = $objPHPExcel->getActiveSheet()->getCell('D3')->getCalculatedValue();
            $uniquecode = explode('-', $data['uniquecode']);
            $data['recordid'] = $uniquecode[2];
            $data['mis'] = $objPHPExcel->getActiveSheet()->getCell('D6')->getCalculatedValue();
            $data['year'] = $objPHPExcel->getActiveSheet()->getCell('D9')->getCalculatedValue();
            $data['month'] = $objPHPExcel->getActiveSheet()->getCell('D10')->getCalculatedValue();
            if($form->isValid($data)) {
                $timesheet = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->find($data['uniquecode']);
                if(!isset($timesheet)) {
                    $timesheet = new Timesheets_Model_Timesheet();
                } else {
                    /*foreach($timesheet->get_activities() as $curActivity) {
                        Zend_Registry::get('entityManager')->remove($curActivity);
                    }
                    $timesheet->get_activities()->clear();
                    Zend_Registry::get('entityManager')->flush();*/
                    if($controller->view->userCanDelete($timesheet) == true) {
                        throw new Exception('Το συγκεκριμένο φύλλο υπάρχει ήδη. Παρακαλώ διαγράψτε το υπάρχον και ξαναπροσπαθήστε.');
                    } else {
                        throw new Exception('Το συγκεκριμένο φύλλο έχει ήδη υποβληθεί και επεξεργαστεί. Παρακαλώ επικοινωνήστε με τον ΕΛΚΕ για την ανανέωση του.');
                    }
                }
                $this->addTimesheetDetails($timesheet, $data);
                $deliverables = $timesheet->get_project()->getTimesheetDeliverables($timesheet->get_employee(), $timesheet->get_monthAsDate());
                $delcolumn = 'B';
                foreach($deliverables as $curDeliverable) {
                    $this->addActivities($objPHPExcel->getActiveSheet(), $timesheet, $curDeliverable, $delcolumn);
                    $delcolumn++;
                }
                return $timesheet;
            } else {
                return array('error' => true, 'errorRow' => 1, 'formElements' => array(), 'message' => 'Τα δεδομένα του template δεν είναι έγκυρα.');
            }
        } else { // Ο χρήστης εισήγαγε άσχετο excel
            return array('error' => true, 'errorRow' => 1, 'formElements' => array(), 'message' => 'Το αρχείο δεν ακολουθεί τη μορφή του template.');
        }
    }

}

?>