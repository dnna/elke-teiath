<?php
require_once('ErgaController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_AitiseisController extends Api_Erga_ErgaController
{
    public function init() {
        parent::init();
        $this->view->section = 'aitiseis';
    }

    public function indexAction() {
        throw new Exception('Δεν υποστηρίζεται');
    }
}
?>