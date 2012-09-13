<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_IndexController extends Zend_Controller_Action {
    public function indexAction() {
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        if(isset($authuser) && $authuser->hasRole('elke')) {
            $this->_helper->redirector('index', 'adminelke');
        } else if(isset($authuser) && $authuser->hasRole('professor')) {
            //$this->_helper->redirector('index', 'adminey');
            $this->_helper->redirector('index', 'mytimesheets');
        } else {
            $this->_helper->redirector('index', 'mytimesheets');
        }
    }
}
?>