<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_IndexController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            $this->view->readonly = true;
        } else {
            $this->view->readonly = false;
        }
    }
    
    public function eventeditAction() {
        $this->_helper->layout->disableLayout();
        $form = new Synedriaseisee_Form_Synedriasi($this->view, true);
        $id = $this->_request->getUserParam('id');
        if($id != null) {
            $em = Zend_Registry::get('entityManager');
            $object = $em->getRepository('Synedriaseisee_Model_Synedriasi')->find($id);
            if($object != null) {
                $form->populate($object);
            }
        }
        $this->view->editeventform = $form;
    }
    
    public function eventviewAction() {
        $this->view->pageTitle = 'Εμφάνιση Συνεδρίασης';
        $id = $this->_request->getUserParam('id');
        if($id != null) {
            $em = Zend_Registry::get('entityManager');
            $object = $em->getRepository('Synedriaseisee_Model_Synedriasi')->find($id);
            if($object != null) {
                $this->view->synedriasi = $object;
            } else {
                throw new Exception('Δεν επιλέχθηκε συνεδρίαση');
            }
        }
    }
    
    public function choosesynedriasiAction() {
        $this->_helper->layout->disableLayout();
        $this->view->form = new Synedriaseisee_Form_ChooseSynedriasi($this->view);
    }
    
    public function icalAction() {
        $front = Zend_Controller_Front::getInstance();
        $apimoduledir = $front->getModuleDirectory('api');
        require_once($apimoduledir.'/controllers/SynedriaseiseeController.php');
        $this->_helper->createIcal($this, Api_SynedriaseiseeController::getEvents());
    }
}
?>