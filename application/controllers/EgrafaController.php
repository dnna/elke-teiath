<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class EgrafaController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->pageTitle = "Έγγραφα";
    }

    public function indexAction()
    {
    }
}