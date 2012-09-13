<?php
require_once('ListsController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_OpprogrammesController extends Api_Lists_ListsController
{
    public function init() {
        parent::init();
        $this->view->type = 'opprogrammes';
        $this->view->classname = 'Application_Model_Lists_OpProgramme';
    }
}
?>