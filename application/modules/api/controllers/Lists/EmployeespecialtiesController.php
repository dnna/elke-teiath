<?php
require_once('ListsController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_EmployeespecialtiesController extends Api_Lists_ListsController
{
    public function init() {
        parent::init();
        $this->view->type = 'employeespecialties';
        $this->view->classname = 'Application_Model_Lists_EmployeeSpecialty';
    }
}
?>