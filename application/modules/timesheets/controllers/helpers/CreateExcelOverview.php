<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Action_Helper_CreateExcelOverview extends Zend_Controller_Action_Helper_Abstract
{
    const STARTCOL = 'B';
    const STARTROW = 7;

    /**
     * @var Erga_Model_Project
     */
    protected $_project;
    protected $_deliverables = array();
    protected $_employees = array();

    protected $_start;
    protected $_end;

    public function direct(Zend_Controller_Action $controller, Erga_Model_Project $project, \EDateTime $start, \EDateTime $end, $attachmentName) {
        $this->_project = $project;
        $this->_start = $start;
        $this->_end = $end;
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(APPLICATION_PATH.'/../public/documents/timesheet_overview.xlsx');

        // Add some data
        $objPHPExcel->getActiveSheet()->SetCellValue('D3', $project->__toString());
        $objPHPExcel->getActiveSheet()->SetCellValue('D4', $project->get_basicdetails()->get_mis());
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', $start->__toString().'-'.$end->__toString());

        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);

        // Add day and deliverable headers
        $this->addSubprojectHeaders($objPHPExcel);
        $this->addEmployeeRows($objPHPExcel);
        $this->setBorders($objPHPExcel);
        // Fix first column width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(65);

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

    protected function addSubprojectHeaders(PHPExcel &$objPHPExcel) {
        $subprojects = $this->_project->get_subprojects();
        $i = 0;
        $sheet = array();
        $sheet[0] = array();
        //$this->blueCell($objPHPExcel, 'A'.self::STARTROW);
        foreach($subprojects as $curSubproject) {
            // If the subproject is out of our period
            /*if($curSubproject->get_subprojectstartdate() < $this->_start || $curSubproject->get_subprojectenddate() > $this->_end) {
                continue; // Skip
            }*/
            $col = self::STARTCOL; for($k = 0; $k < $i; $k++) { $col++; }
            $this->pinkCell($objPHPExcel, $col.self::STARTROW);
            $sheet[0][$i] = $curSubproject->get_subprojecttitle();
            $newi = $this->addDeliverableHeaders($objPHPExcel, $curSubproject, $col);
            $endcol = $col;
            for($k = $i+1; $k < ($i + $newi); $k++) { $sheet[0][$k] = ''; $endcol++; }
            // Merge the subproject cells
            if($col != $endcol) {
                $objPHPExcel->getActiveSheet()->mergeCells($col.self::STARTROW.':'.$endcol.self::STARTROW);
            }
            $objPHPExcel->getActiveSheet()->getStyle($col.self::STARTROW)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            // Increment the columns to the next subproject
            $i = $i + $newi;
        }
        $objPHPExcel->getActiveSheet()->fromArray($sheet, null, self::STARTCOL.self::STARTROW);
    }

    protected function addDeliverableHeaders(PHPExcel &$objPHPExcel, Erga_Model_Subproject $subproject, $startcol) {
        $workpackages = $subproject->get_workpackagesNatsort();
        $i = 0;
        $sheet = array();
        $sheet[0] = array();
        //$this->blueCell($objPHPExcel, 'A'.self::STARTROW);
        foreach($workpackages as $curWorkpackage) {
            foreach($curWorkpackage->get_deliverablesNatsort() as $curDeliverable) {
                /*if($curDeliverable->get_startdate() < $this->_start || $curDeliverable->get_enddate() > $this->_end) {
                    continue; // Skip
                }*/
                $col = $startcol; for($k = 0; $k < $i; $k++) { $col++; }
                $this->blueCell($objPHPExcel, $col.(self::STARTROW+1));
                $sheet[0][$i] = $curDeliverable->get_shorttitle();
                $this->_deliverables[] = $curDeliverable;
                //$this->addActivities($objPHPExcel, $sheet, $curWorkpackage, $i);
                $i++;
            }
        }
        $objPHPExcel->getActiveSheet()->fromArray($sheet, null, $startcol.(self::STARTROW+1));
        return $i;
    }

    protected function addEmployeeRows(PHPExcel &$objPHPExcel) {
        $activesheet = $objPHPExcel->getActiveSheet();
        $sheet = array();
        $employees = $this->_project->get_employees();
        $row = self::STARTROW + 2;
        $i = 0;
        foreach($employees as $curEmployee) {
            /*if($curEmployee->get_startdate() < $this->_start || $curEmployee->get_enddate() > $this->_end) {
                continue; // Skip
            }*/
            $this->blueCell($objPHPExcel, 'A'.$row);
            $sheet[$i] = array();
            $sheet[$i][0] = $curEmployee->get_employee()->get_name().' '.$curEmployee->get_startdate().'–'.$curEmployee->get_enddate();
            $this->_employees[] = $curEmployee;
            // Φτιάχνουμε τα cell types και το style
            $activesheet->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->addTimesheetTotals($objPHPExcel, $curEmployee, $row);
            $row++;
            $i++;
        }
        $activesheet->fromArray($sheet, null, 'A'.(self::STARTROW+2));
        // Apply border
        $style_header = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('A'.(self::STARTROW + 2).':'.'A'.($row-1))->applyFromArray($style_header);
    }

    protected function addTimesheetTotals(PHPExcel &$objPHPExcel, Erga_Model_SubItems_SubProjectEmployee $employee, $row) {
        $activesheet = $objPHPExcel->getActiveSheet();
        $sheet = array();
        $sheet[0] = array();
        $timesheets = $employee->get_timesheetsApproved();
        $i = 0;
		foreach($this->_deliverables as $i => $curDeliverable) {
			$sum = 0;
			foreach($timesheets as $curTimesheet) {
				foreach($curTimesheet->get_activitiesForDeliverable($curDeliverable) as $curActivity) {
					/*if($curActivity->get_date() < $this->_start || $curActivity->get_date() > $this->_end) {
						continue; // Skip
					}*/
					$sum = $sum + $curActivity->getHours();
					$sheet[0][$i] = $sum;
				}
			}
		}
        $activesheet->fromArray($sheet, null, self::STARTCOL.$row);
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

    protected function setBorders(PHPExcel &$objPHPExcel) {
        $topleft = self::STARTCOL.self::STARTROW;
        $newcol = self::STARTCOL; for($i = 0; $i < count($this->_deliverables)-1; $i++) { $newcol++; };
        $bottomright = $newcol.(self::STARTROW+count($this->_employees)+1);
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