<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_ContractorsController extends Api_IndexController
{
    const name = 'Ανάδοχοι';

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
        $limit = $this->getRequest()->getParam('limit');

        $this->view->contractors = Zend_Registry::get('entityManager')
                        ->getRepository('Application_Model_Contractor')
                        ->findContractors($filters, $limit);
        // Εύρεση των πεδίων
        $form = new Application_Form_Contractor($this->view);
        $properties = array();
        foreach($form->getElements() as $curName => $curElement) {
            $properties[$curName] = 'get_'.$curName;
        }
        unset($properties['name']);
        unset($properties['afm']);
        unset($properties['id']);

        $this->_helper->Index($this, $this->view->contractors, 'contractors', array('afm' => 'get_afm'), $properties);
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Contractor')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Ο ανάδοχος δεν βρέθηκε.', 404);
        }
        $form = new Application_Form_Contractor($this->view);
        $form->setName('default');
        $this->_helper->Get($this, $object, $form, 'contractor');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Contractor')->find($this->_request->getParam('id'));
        $form = new Application_Form_Contractor($this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Contractor')->find($this->_request->getParam('id'));
        $form = new Application_Form_Contractor($this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Application_Model_Contractor', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Application_Form_Contractor($this->view));
    }
}
?>