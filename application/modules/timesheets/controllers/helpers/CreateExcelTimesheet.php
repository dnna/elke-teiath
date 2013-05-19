<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Action_Helper_CreateExcelTimesheet extends Zend_Controller_Action_Helper_Abstract
{
    const STARTCOL = 'B';
    const STARTROW = 12;

    /**
     * @var Timesheets_Model_Timesheet
     */
    protected $_timesheet;
    protected $_deliverablesCount;

    public function direct(Zend_Controller_Action $controller, Timesheets_Model_Timesheet $timesheet, $attachmentName) {
        $this->_timesheet = $timesheet;
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(APPLICATION_PATH.'/../public/documents/timesheet.xlsx');

        // Add some data
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', $timesheet->generateId());
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', $timesheet->get_employee()->__toString());
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', $timesheet->get_employee()->get_employee()->get_afm());
        // Educational project stuff
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        if($timesheet->get_project() != null && $timesheet->get_project()->get_projectid() == $options['project']['educational']) {
            $objPHPExcel->getActiveSheet()->SetCellValue('D6', 'Εκπαιδευτικό Έργο');
            $objPHPExcel->getActiveSheet()->SetCellValue('D7', '');
            $objPHPExcel->getActiveSheet()->SetCellValue('D8', '');
        } else {
            $objPHPExcel->getActiveSheet()->SetCellValue('D6', $timesheet->get_project()->get_basicdetails()->get_mis());
            $objPHPExcel->getActiveSheet()->SetCellValue('D7', $timesheet->get_employee()->get_contractnum());
            $objPHPExcel->getActiveSheet()->SetCellValue('D8', $timesheet->get_employee()->get_startdate().' — '.$timesheet->get_employee()->get_enddate());
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('D9', $timesheet->get_year());
        $objPHPExcel->getActiveSheet()->SetCellValue('D10', $timesheet->get_month());

        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

        // Add day and deliverable headers
        $this->addDeliverableHeaders($objPHPExcel);
        $this->addDayRows($objPHPExcel);
        $this->setBorders($objPHPExcel);

        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $tmpfname = tempnam(Zend_Registry::get('cachePath'), 'elkeexcel');
        $objWriter->save($tmpfname);
        $content = file_get_contents($tmpfname);

        // Echo done
        unlink($tmpfname);

        $controller->getHelper('layout')->disableLayout();
        $controller->getHelper('viewRenderer')->setNoRender(TRUE);
        $controller->getResponse()
             ->setHeader('Content-Description', 'File Transfer')
             ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
             ->setHeader('Content-Disposition', 'attachment; filename='.$attachmentName)
             ->setHeader('Content-Transfer-Encoding', 'binary')
             ->setHeader('Expires', '0')
             ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
             ->setHeader('Pragma', 'public')
             ->setHeader('Content-Length', $controller->getHelper('getBinaryDataSize')->direct($content));

        echo $content;
    }

    protected function addDeliverableHeaders(PHPExcel &$objPHPExcel) {
        $deliverables = $this->_timesheet->get_project()->getTimesheetDeliverables($this->_timesheet->get_employee(), $this->_timesheet->get_monthAsDate());
        $this->_deliverablesCount = count($deliverables);
        $i = 0;
        $sheet = array();
        $sheet[0] = array();
        $this->blueCell($objPHPExcel, 'A'.self::STARTROW);
        foreach($deliverables as $curDeliverable) {
            $newcol = self::STARTCOL; for($k = 0; $k < $i; $k++) { $newcol++; }
            $this->blueCell($objPHPExcel, $newcol.self::STARTROW);
            $sheet[0][$i] = $curDeliverable->get_shorttitle();
            $this->addActivities($objPHPExcel, $sheet, $curDeliverable, $i);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->fromArray($sheet, null, self::STARTCOL.self::STARTROW);
    }

    protected function addDayRows(PHPExcel &$objPHPExcel) {
        $activesheet = $objPHPExcel->getActiveSheet();
        $sheet = array();
        $day = $this->_timesheet->get_monthAsDate();
        $row = self::STARTROW + 1;
        for($i = 1; $i <= $this->_timesheet->get_monthAsDate()->format('t'); $i++) {
            $sheet[$i] = array();
            $sheet[$i][0] = $i;
            // Γκριζάρουμε τις ημέρες πριν αρχίσει η σύμβαση
            if($this->isOutsideContract($day)) {
                $this->grayCell($objPHPExcel, 'A'.$row);
            } else {
                // Γκριζάρουμε αλλά ΔΕΝ κλειδώνουμε τα Σαββατοκύριακα
                if($this->isWeekend($day)) {
                    $this->pinkCell($objPHPExcel, 'A'.$row);
                } else {
                    $this->blueCell($objPHPExcel, 'A'.$row);
                }
            }
            // Φτιάχνουμε τα cell types και το style
            //for ($j = 0; $j <= $this->_deliverablesCount; $j++) {
                $activesheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //}
            $day->add(date_interval_create_from_date_string('1 day'));
            $row++;
        }
        $activesheet->fromArray($sheet, null, 'A'.(self::STARTROW+1));
    }

    protected function addActivities(PHPExcel &$objPHPExcel, &$sheet, Erga_Model_SubItems_Deliverable $deliverable, $col) {
        for($i = 1; $i <= $this->_timesheet->get_monthAsDate()->format('t'); $i++) {
            if(!isset($sheet[$i])) {
                $sheet[$i] = array();
            }
            $sheet[$i][$col] = '';
            foreach($this->_timesheet->get_activitiesForDeliverable($deliverable) as $curActivity) {
                if($i == $curActivity->get_day()) {
                    $sheet[$i][$col] = $curActivity->get_startAsDate()->format('H:i').'-'.$curActivity->get_endAsDate()->format('H:i');
                }
            }
            $day = new EDateTime($this->_timesheet->get_year().'-'.$this->_timesheet->get_month().'-'.$i);
            $newcol = self::STARTCOL; for($k = 0; $k < $col; $k++) { $newcol++; }
            // Γκριζάρουμε και κλειδώνουμε τις μέρες πριν την αρχή της σύμβασης
            if($this->isOutsideContract($day) || $day < $deliverable->get_startdate() || $day > $deliverable->get_enddate()) {
                $this->grayCell($objPHPExcel, ($newcol).(self::STARTROW+$i));
            } else {
                // Γκριζάρουμε αλλά ΔΕΝ κλειδώνουμε τα Σαββατοκύριακα
                if($this->isWeekend($day)) {
                    $this->pinkCell($objPHPExcel, ($newcol).(self::STARTROW+$i));
                }
                $objPHPExcel->getActiveSheet()->getStyle(($newcol).(self::STARTROW+$i))->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
            }
        }
    }

    protected function grayCell(PHPExcel &$objPHPExcel, $cell) {
        $style_header = array(
                'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb'=>'808080'),
                ),
            );
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style_header);
    }

    protected function blueCell(PHPExcel &$objPHPExcel, $cell) {
        $style_header = array(
                'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb'=>'D1EEEE'),
                ),
            );
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style_header);
    }

    protected function pinkCell(PHPExcel &$objPHPExcel, $cell) {
        $style_header = array(
                'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb'=>'F7B3DA'),
                ),
            );
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style_header);
    }

    protected function isWeekend(EDateTime $day) {
        $dow = $day->format('w');
        if($dow == 0 || $dow == 6) {
            return true;
        } else {
            return false;
        }
    }

    protected function isOutsideContract(EDateTime $day) {
        $startdate = $this->_timesheet->get_employee()->get_startdate();
        $enddate = $this->_timesheet->get_employee()->get_enddate();
        if($day < $startdate || $day > $enddate) {
            return true;
        } else {
            return false;
        }
    }

    protected function setBorders(PHPExcel &$objPHPExcel) {
        $topleft = 'A'.self::STARTROW;
        $newcol = 'A'; for($i = 0; $i < $this->_deliverablesCount; $i++) { $newcol++; };
        $bottomright = $newcol.(self::STARTROW+$this->_timesheet->get_monthAsDate()->format('t'));
        $default_border = array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
        );

        $style_header = array(
                'borders' => array(
                    'allborders' => $default_border,
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle($topleft.':'.$bottomright)->applyFromArray($style_header);
        $objPHPExcel->getActiveSheet()->getStyle($topleft.':'.$bottomright)->getNumberFormat()->setFormatCode('@');
    }
}
?>