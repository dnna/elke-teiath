<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->pageTitle = "Αρχική Σελίδα";
    }

    public function indexAction()
    {
        if($this->view->getUserInfo() == false){
            $this->_helper->redirector('index','login');
        }
    }
}