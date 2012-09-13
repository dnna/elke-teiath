<?php
require_once('ListsController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_ExpenditurecategoriesController extends Api_Lists_ListsController
{
    public function init() {
        parent::init();
        $this->view->type = 'expenditurecategories';
        $this->view->classname = 'Application_Model_Lists_ExpenditureCategory';
    }
}
?>