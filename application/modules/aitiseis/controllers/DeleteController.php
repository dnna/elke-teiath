<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_DeleteController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Επεξεργασία Αίτησης";
    }

    public function postDispatch() {
        if(isset($this->view->type)) {
            $this->view->reversetype = $this->_helper->getReverseMapping($this->view->type);
        } else {
            $this->view->reversetype = 'ypovoliaitimatos';
        }
    }

    public function indexAction() {
        $aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        if(!isset($this->view->type) && $aitisi != null) {
            $this->view->type = get_class($aitisi);
        }
        if($aitisi != null && $aitisi->isDeletable() != true) {
            $this->_helper->viewRenderer('deletefail');
            return;
        } else {
            return $this->_helper->deleteHelper($this, 'aitisiid', $this->view->type, 'aitisi');
        }
    }
}

?>