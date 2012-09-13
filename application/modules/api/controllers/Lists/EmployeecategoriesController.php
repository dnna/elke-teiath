<?php
require_once('ListsController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_EmployeecategoriesController extends Api_Lists_ListsController
{
    public function init() {
        parent::init();
        $this->view->type = 'employeecategories';
        $this->view->classname = 'Application_Model_Lists_EmployeeCategory';
    }
}
?>