<?php
require_once APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';

/**
 * Παίρνει ένα αντικείμενο και εμφανίζει τα πεδία του σαν στήλες ενός αρχείου
 * excel.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Action_Helper_CreateExcel extends Zend_Controller_Action_Helper_Abstract
{
    protected function addHeaders(PHPExcel &$objPHPExcel, $headers) {
        // Bold Style
        $styleArray = array(
            'font' => array(
            'bold' => true
            )
        );
        $col = 'A';
        foreach($headers as $curHeader) {
            $options = array();
            if(isset($curHeader[1]) && is_array($curHeader[1])) {
                $options = $curHeader[1];
                $curHeader = $curHeader[0];
            }
            $objPHPExcel->getActiveSheet()->SetCellValue($col.'1', $curHeader); // Προσθήκη του header
            // Width
            if(isset($options['width'])) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($options['width']);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }

            // Περνάμε το Bold Style στα headers
            $objPHPExcel->getActiveSheet()->getStyle($col.'1')->applyFromArray($styleArray);
            ++$col;
        }
    }

    protected function addData(PHPExcel &$objPHPExcel, $data, $row = 2) {
        $col = 'A';
        foreach($data as $curCol) {
            $objPHPExcel->getActiveSheet()->SetCellValue($col.$row, $curCol);
            ++$col;
        }
    }

    public function direct(Zend_Controller_Action $controller, $headers, $data, $attachmentName, $title = "Έργα ΕΛΚΕ") {
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("ΠΣ ΕΛΚΕ");
        $objPHPExcel->getProperties()->setLastModifiedBy("ΠΣ ΕΛΚΕ");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        $this->addHeaders($objPHPExcel, $headers);
        $row = 2;
        foreach($data as $rowData) {
            $this->addData($objPHPExcel, $rowData, $row);
            $row++;
        }
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($title);

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
}
?>