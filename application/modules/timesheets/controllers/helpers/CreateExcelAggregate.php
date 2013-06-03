<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Action_Helper_CreateExcelAggregate extends Zend_Controller_Action_Helper_Abstract
{
    const STARTCOL = 'B';
    const STARTROW = 7;

    protected $dayRows = array();

    /**
     * @var Timesheets_Model_Timesheet
     */
    protected $_employee;
    protected $_symvaseis = array();
    protected $_year;
    protected $_type;

    public function direct(Zend_Controller_Action $controller, Application_Model_Employee $employee, $year, $type, $attachmentName) {
        $this->_employee = $employee;
        $this->_year = $year;
        $this->_type = $type;
        $overview = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->getOverview($this->_employee, array('year' => $this->_year));
        foreach($overview['symvaseis'] as $curContract) { // Φιλτράρουμε τις συμβάσεις ώστε να κρατήσουμε μόνο όσες έχουν ΜΦΠ
            if(count($curContract->get_timesheetsApproved($this->_year)) > 0) {
                $this->_symvaseis[] = $curContract;
            }
        }
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(APPLICATION_PATH.'/../public/documents/timesheet_aggregate.xlsx');

        // Add some data
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', $employee->__toString());
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', $employee->get_afm());
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', $this->_year);

        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

        // Add day and deliverable headers
        $this->addDayRows($objPHPExcel);
        $this->addProjectHeaders($objPHPExcel);
        $this->setBorders($objPHPExcel);
        $toCol = 'A'; for($i = 0; $i < count($this->_symvaseis); $i++) { $toCol++; };
        for($i = 'B'; $i <= $toCol; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
        }
        $objPHPExcel->getActiveSheet()->calculateColumnWidths();

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

    protected function addProjectHeaders(PHPExcel &$objPHPExcel) {
        $i = 0;
        $sheet = array();
        $sheet[0] = array();
        $this->blueCell($objPHPExcel, 'A'.self::STARTROW);
        foreach($this->_symvaseis as $curContract) {
            $newcol = self::STARTCOL; for($k = 0; $k < $i; $k++) { $newcol++; }
            $this->addActivities($objPHPExcel, $curContract, $newcol);
            $this->blueCell($objPHPExcel, $newcol.self::STARTROW);
            $sheet[0][$i] = $curContract->getProjectName().' '.$curContract->get_startdate().'–'.$curContract->get_enddate();
            $i++;
        }
        $objPHPExcel->getActiveSheet()->fromArray($sheet, null, self::STARTCOL.self::STARTROW);
    }

    protected function addDayRows(PHPExcel &$objPHPExcel) {
        $activesheet = $objPHPExcel->getActiveSheet();
        $sheet = array();
        $row = self::STARTROW + 1;
        for($m = 1; $m <= 12; $m++) {
            $day = new \EDateTime($this->_year.'-'.$m.'-1');
            $this->dayRows[$m] = array();
            $sheet[$row][0] = $day->format('M');
            $activesheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $row++;
            $i = 1;
            while($i <= (int)($day->format('j'))) {
                $sheet[$row] = array();
                $sheet[$row][0] = $i;
                // Φτιάχνουμε τα cell types και το style
                //for ($j = 0; $j <= $this->_deliverablesCount; $j++) {
                    $activesheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //}
                if($this->isWeekend($day)) {
                    $this->pinkCell($objPHPExcel, 'A'.$row);
                } else {
                    $this->blueCell($objPHPExcel, 'A'.$row);
                }
                $day->add(date_interval_create_from_date_string('1 day'));
                $this->dayRows[$m][$i] = $row;
                $row++;
                $i++;
            }
        }
        $activesheet->fromArray($sheet, null, 'A'.(self::STARTROW+1));
    }

    protected function addActivities(PHPExcel &$objPHPExcel, Erga_Model_SubItems_SubProjectEmployee $curContract, $col) {
        $timesheets = $curContract->get_timesheetsApproved($this->_year);
        foreach($timesheets as $curTimesheet) {
            foreach($curTimesheet->get_activities() as $curActivity) {
                //if($objPHPExcel->getActiveSheet()->getCell($col.$this->getRowForActivity($curActivity))->getValue() == '')
                if($this->_type == 'schedule') {
                    $hours = $curActivity->get_startAsDate()->format('H:i').'-'.$curActivity->get_endAsDate()->format('H:i');
                } else {
                    $hours = $curActivity->getHours();
                }
                $objPHPExcel->getActiveSheet()->SetCellValue($col.$this->getRowForActivity($curActivity), round($hours, 2));
                if($this->isWeekend($curActivity->get_date())) {
                    $this->pinkCell($objPHPExcel, $col.$this->getRowForActivity($curActivity));
                } else {
                    $this->blueCell($objPHPExcel, $col.$this->getRowForActivity($curActivity));
                }
                $objPHPExcel->getActiveSheet()->getStyle($col.$this->getRowForActivity($curActivity))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        //$objPHPExcel->getActiveSheet()->fromArray($sheet, null, 'A'.(self::STARTROW+1));
    }

    protected function getRowForActivity(Timesheets_Model_Activity $curActivity) {
        return $this->dayRows[$curActivity->get_timesheet()->get_month()][$curActivity->get_day()];
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

    private function getArrCount($arr, $depth = 1) {
        if (!is_array($arr) || !$depth)
            return 0;

        $res = count($arr);

        foreach ($arr as $in_ar)
            $res+=$this->getArrCount($in_ar, $depth - 1);

        return $res;
    }

    protected function setBorders(PHPExcel &$objPHPExcel) {
        $topleft = 'A'.self::STARTROW;
        $newcol = 'A'; for($i = 0; $i < count($this->_symvaseis); $i++) { $newcol++; };
        $bottomright = $newcol.(self::STARTROW+$this->getArrCount($this->dayRows, 2));
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