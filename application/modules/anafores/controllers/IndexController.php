<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_IndexController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Αναφορές";
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $auth->getStorage()->read()->hasRole('professor')) {
            $this->_helper->viewRenderer('indexprofessor');
        } else if($auth->hasIdentity() && $auth->getStorage()->read()->hasRole('elke')) {
            $this->_helper->viewRenderer('indexelke');
        }
    }
}

?>