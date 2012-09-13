<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Aitiseis_IndexController extends Api_IndexController
{
    const name = 'Αιτήσεις';

    public function init() {
        parent::init();
        $front = Zend_Controller_Front::getInstance();
        $aitiseismoduledir = $front->getModuleDirectory('aitiseis');
        require_once($aitiseismoduledir.'/controllers/helpers/GetMapping.php');
        require_once($aitiseismoduledir.'/controllers/helpers/GetAitiseisTypes.php');
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->type = $this->_request->getParam('subtype');
    }

    public function indexAction() {
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $this->view->classname = $mappinghelper->direct($this->view->type);
        if(isset($this->view->type)) {
            $auth = Zend_Auth::getInstance();
            $name = $this->utf8_urldecode($this->getRequest()->getParam('name'));
            if($name == null) {
                $name = $this->utf8_urldecode($this->getRequest()->getParam('q'));
            }
            if(!$auth->hasIdentity()) {
                throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
            } else if(!$auth->getStorage()->read()->hasRole('elke')) {
                $aitiseis = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->findAitiseis(array(
                    'creator' => $auth->getStorage()->read()->get_userid(),
                    'search' => $name,
                    'approved' => $this->_request->getParam('approved'),
                ));
            } else {
                $aitiseis = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->findAitiseis(array(
                    'search' => $name,
                    'approved' => $this->_request->getParam('approved'),
                ));
            }
        } else {
            // Εμφανίζουμε απλά τους τύπους αιτήσεων
            $aitiseistypes = new Aitiseis_Action_Helper_GetAitiseisTypes();
            $aitiseis = array();
            foreach($aitiseistypes->direct() as $curType => $curTypeName) {
                $aitiseis[] = new Dnna_Model_ApiIndex($curType, $curTypeName);
            }
        }
        $this->_helper->Index($this, $aitiseis, 'aitiseis', array('aitisiid' => 'get_aitisiid'));
    }

    public function getAction() {
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $this->view->classname = $mappinghelper->direct($this->view->type);
        $object = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $formclass = $object::formclass;
        $form = new $formclass($object, $this->view);
        $this->addApprovalFields($form, $object);
        $this->_helper->Get($this, $object, $form, 'aitisi');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $classname = $mappinghelper->direct($this->view->type);
        $object = new $classname();
        $formclass = $object::formclass;
        $form = new $formclass($object, $this->view);
        $this->addApprovalFields($form, $object);
        $this->_helper->PostOrPut($this, $classname, $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $classname = $mappinghelper->direct($this->view->type);
        $object = new $classname();
        $formclass = $object::formclass;
        $form = new $formclass($object, $this->view);
        $this->addApprovalFields($form, $object);
        $this->_helper->PostOrPut($this, $classname, $form, $this->_request->getParam('id'));
    }
    
    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $classname = $mappinghelper->direct($this->view->type);
        $this->_helper->Delete($this, $classname, $this->_request->getParam('id'));
    }

    public function schemaAction() {
        if(!isset($this->view->type)) {
            throw new Exception('Για να δείτε το schema πρέπει να επιλέξετε τύπο αίτησης.');
        }
        $mappinghelper = new Aitiseis_Action_Helper_GetMapping();
        $aitisiclass = $mappinghelper->direct($this->view->type);
        $aitisi = new $aitisiclass();
        $formclass = $aitisiclass::formclass;
        $form = new $formclass($aitisi, $this->view);
        $this->addApprovalFields($form, $aitisi);
        echo $this->_helper->generateXsd($this, $form, 'aitisi');
    }

    protected function addApprovalFields(Zend_Form &$form, $aitisi) {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $auth->getStorage()->read()->hasRole('elke')) {
            $approvalform = new Aitiseis_Form_ChangeApproval($aitisi);
            $form->addElements($approvalform->getElements());
            $form->addSubForms($approvalform->getSubForms());
        }
    }
}
?>