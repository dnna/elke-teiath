<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_YpoergaController extends Api_IndexController
{
    const name = 'Υποέργα';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }
    
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $name = $this->utf8_urldecode($this->getRequest()->getParam('name'));
        $projectid = $this->getRequest()->getParam('projectid');
        if($name == null) {
            $name = $this->utf8_urldecode($this->getRequest()->getParam('q'));
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if(!$auth->getStorage()->read()->hasRole('elke')) {
            $projects = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->findSubprojects(array(
                'supervisoruserid' => $auth->getStorage()->read()->get_userid(),
                'search' => $name,
                'projectid' => $projectid,
            ));
        } else {
            $projects = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->findSubprojects(array(
                'search' => $name,
                'projectid' => $projectid,
            ));
        }
        $this->_helper->Index($this, $projects, 'subprojects', array('subprojectid' => 'get_subprojectid'));
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->_request->getParam('id'));
        $this->view->subproject = $object;
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = new Erga_Form_Ypoerga_Ypoerga();
        $this->_helper->Get($this, $object, $form, 'subproject');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Ypoerga_Ypoerga();
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Ypoerga_Ypoerga();
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Erga_Model_SubProject', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Erga_Form_Ypoerga_Ypoerga(), 'subproject');
    }
}
?>