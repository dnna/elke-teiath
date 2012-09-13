<?php
require_once('ErgaController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_FinancialdetailsController extends Api_Erga_ErgaController
{
    public function init() {
        parent::init();
        $this->view->section = 'financialdetails';
    }

    public function indexAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }
}
?>