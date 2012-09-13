<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_ErgaController extends Api_IndexController
{
    const name = 'Έργα';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->section = null;
    }
    
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $name = $this->utf8_urldecode($this->getRequest()->getParam('name'));
        if($name == null) {
            $name = $this->utf8_urldecode($this->getRequest()->getParam('q'));
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if(!$auth->getStorage()->read()->hasRole('elke')) {
            $projects = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findProjects(array(
                'supervisoruserid' => $auth->getStorage()->read()->get_userid(),
                'search' => $name,
            ));
        } else {
            $projects = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findProjects(array(
                'search' => $name,
            ));
        }
        $this->_helper->Index($this, $projects, 'projects', array('projectid' => 'get_projectid'));
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = new Erga_Form_Project($this->view->section, $this->view);
        $this->_helper->Get($this, $object, $form, 'project');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Project($this->view->section, $this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Project($this->view->section, $this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Erga_Model_Project', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Erga_Form_Project($this->view->section, $this->view), 'project');
    }
}
?>