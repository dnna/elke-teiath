<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Synedriaseisee_SubjectsController extends Api_IndexController
{
    const name = 'Θέματα Συνεδριάσεων ΕΕΕ';

    public function init() {
        $this->_allowAnonymous = true;
        parent::init();
    }

    public function indexAction() {
        $filters = $this->getRequest()->getParams();
        $this->view->subjects = Zend_Registry::get('entityManager')
                        ->getRepository('Synedriaseisee_Model_Subject')
                        ->findSubjects($filters);

        if($this->_request->getParam('nowrap') != null && $this->_request->getParam('nowrap') === 'true') {
            $this->view->nowrap = true;
        }
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $object = Zend_Registry::get('entityManager')->getRepository('Synedriaseisee_Model_Subject')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Η συνεδρίαση δεν βρέθηκε.', 404);
        }
        $form = new Synedriaseisee_Form_Subject(0, $this->view);
        $form->setName('default');
        $this->_helper->Get($this, $object, $form, 'subject');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $form = new Synedriaseisee_Form_Subject(0, $this->view);
        $form->setName('default');
        $type = $this->findSubjectType($this->_request->getUserParams());
        $id = $this->findSubjectId(null, $type);
        $this->_helper->PostOrPut($this, $type, $form, $id);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $form = new Synedriaseisee_Form_Subject(0, $this->view);
        $form->setName('default');
        $form->setRequired(false);
        $type = $this->findSubjectType($this->_request->getUserParams());
        $id = $this->findSubjectId($this->_request->getParam('id'), $type);
        $this->_helper->PostOrPut($this, $type, $form, $id);
    }

    protected function findSubjectId($defaultid = null, $type = null) {
        $id = $defaultid;
        $synedriasiparam = $this->_request->getUserParam('synedriasi');
        if($synedriasiparam != null) {
            if(isset($synedriasiparam['id'])) {
                $synedriasi = Zend_Registry::get('entityManager')->getRepository('Synedriaseisee_Model_Synedriasi')->find($synedriasiparam['id']);
                $subject = $synedriasi->findSubject($this->_request->getUserParam('num'));
                if($subject != null) {
                    if(get_class($subject) === $type) {
                        $id = $subject->get_recordid();
                    } else {
                        // Remove the old one so it can be replaced
                        if($subject->get_synedriasi() != null) {
                            $subject->get_synedriasi()->get_subjects()->removeElement($subject);
                        }
                        $subject->set_synedriasi(null);
                        $subject->remove();
                    }
                }
            }
        }
        return $id;
    }

    protected function findSubjectType($options) {
        if(isset($options['default']) && count($options['default']) > 0) {
            $options = array_merge($options, $options['default']);
            unset($options['default']);
        }
        if(isset($options['aitisi']) && isset($options['aitisi']['aitisiid']) && 
                $options['aitisi']['aitisiid'] != '' && $options['aitisi']['aitisiid'] !== 'null') {
            return 'Synedriaseisee_Model_AitisiSubject';
        } else {
            return 'Synedriaseisee_Model_SimpleSubject';
        }
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Synedriaseisee_Model_Subject', $this->_request->getParam('id'));
    }
}
?>