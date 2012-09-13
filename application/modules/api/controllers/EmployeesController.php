<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_EmployeesController extends Api_IndexController
{
    const name = 'Απασχολούμενοι';

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
        if(isset($filters['surname'])) {
            $filters['surname'] = $this->utf8_urldecode($filters['surname']);
        }
        $limit = $this->getRequest()->getParam('limit');

        $this->view->employees = Zend_Registry::get('entityManager')
                        ->getRepository('Application_Model_Employee')
                        ->findEmployees($filters, $limit);
        // Εύρεση των πεδίων
        $form = new Application_Form_Employee($this->view);
        $properties = array();
        foreach($form->getElements() as $curName => $curElement) {
            $properties[$curName] = 'get_'.$curName;
        }
        unset($properties['afm']);

        $this->_helper->Index($this, $this->view->employees, 'employees', array('afm' => 'get_afm'), $properties);
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Ο απασχολούμενος δεν βρέθηκε.', 404);
        }
        $form = new Application_Form_Employee($this->view);
        $form->setName('default');
        $this->_helper->Get($this, $object, $form, 'employee');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->find($this->_request->getParam('id'));
        $form = new Application_Form_Employee($this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->find($this->_request->getParam('id'));
        $form = new Application_Form_Employee($this->view);
        $form->setName('default');
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Application_Model_Employee', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Application_Form_Employee($this->view));
    }
}
?>