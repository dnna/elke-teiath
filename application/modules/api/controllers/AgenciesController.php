<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_AgenciesController extends Api_IndexController
{
    const name = 'Φορείς (συνεργαζόμενοι φορείς, φορείς χρηματοδότησης κ.α.)';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function indexAction() {
        // Αυτή η λειτουργία επιτρέπεται μόνο σε αυθεντικοποιημένους χρήστες
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('elke') && !$auth->getStorage()->read()->hasRole('professor'))) {
            return;
        }

        $filters = $this->getRequest()->getParams();
        if(isset($filters['name'])) {
            $filters['name'] = $this->utf8_urldecode($filters['name']);
        }
        $limit = $this->getRequest()->getParam('limit'); // Για το Zend_Paginator

        $this->view->agencies = Zend_Registry::get('entityManager')
                        ->getRepository('Application_Model_Lists_Agency')
                        ->findAgencies($filters);
        $this->_helper->Index($this, $this->view->agencies, 'agencies');
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_Agency')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Ο φορέας δεν βρέθηκε.', 404);
        }
        $form = new Dnna_Form_AutoForm('Application_Model_Lists_Agency', $this->view);
        $form->setName('default');
        $this->_helper->Get($this, $object, $form, 'agency');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_Agency')->find($this->_request->getParam('id'));
        $form = new Dnna_Form_AutoForm('Application_Model_Lists_Agency', $this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_Agency')->find($this->_request->getParam('id'));
        $form = new Dnna_Form_AutoForm('Application_Model_Lists_Agency', $this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Application_Model_Lists_Agency', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Dnna_Form_AutoForm('Application_Model_Lists_Agency', $this->view));
    }
}
?>