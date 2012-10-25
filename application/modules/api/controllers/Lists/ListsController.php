<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_ListsController extends Api_IndexController
{
    const noindex = true;
    const name = 'Λίστες';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function indexAction() {
        if(isset($this->view->classname)) {
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository($this->view->classname)
                                    ->getList();
        } else {
            // Εμφανίζουμε απλά τους τύπους λιστών
            $this->view->list = array();
            $this->view->list[] = new Dnna_Model_ApiIndex('projectcategories', 'Κατηγορίες Έργων');
            $this->view->list[] = new Dnna_Model_ApiIndex('expenditurecategories', 'Κατηγορίες Δαπανών');
            $this->view->list[] = new Dnna_Model_ApiIndex('opprogrammes', 'Επιχειρισιακά Προγράμματα');
            $this->view->list[] = new Dnna_Model_ApiIndex('fundingframeworks', 'Πλαίσια Χρηματοδότησης');
            $this->view->list[] = new Dnna_Model_ApiIndex('employeecategories', 'Κατηγορίες Απασχολούμενων');
            $this->view->list[] = new Dnna_Model_ApiIndex('employeespecialties', 'Ειδικότητες Απασχολούμενων');
            $this->view->list[] = new Dnna_Model_ApiIndex('banks', 'Τράπεζες');
        }
        $this->_helper->Index($this, $this->view->list);
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = new Dnna_Form_AutoForm(get_class($object), $this->view);
        $this->_helper->Get($this, $object, $form);
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->find($this->_request->getParam('id'));
        $form = new Dnna_Form_AutoForm(get_class($object), $this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->find($this->_request->getParam('id'));
        $form = new Dnna_Form_AutoForm(get_class($object), $this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, $this->view->classname, $this->_request->getParam('id'));
    }

    public function schemaAction() {
        if(!isset($this->view->classname)) {
            throw new Exception('Για να δείτε το schema πρέπει να επιλέξετε τύπο λίστας.');
        }
        echo $this->_helper->generateXsd($this, new Dnna_Form_AutoForm($this->view->classname, $this->view));
    }
}
?>