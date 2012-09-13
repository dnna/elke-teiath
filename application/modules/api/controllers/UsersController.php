<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_UsersController extends Api_IndexController
{
    const name = 'Χρήστες (Read-Only)';

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

        $name = $this->utf8_urldecode($this->getRequest()->getParam('name'));
        if($name == null) {
            $name = $this->utf8_urldecode($this->getRequest()->getParam('q'));
        }
        $limit = $this->getRequest()->getParam('limit');
        if($limit == null) {
            $limit = $this->getRequest()->getParam('s');
        }

        $this->view->users = Zend_Registry::get('entityManager')
                        ->getRepository('Application_Model_User')
                        ->findByName($name, $limit);
        $this->_helper->Index($this, $this->view->users, 'users', array('userid' => 'get_userid'));
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $object = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Ο χρήστης δεν βρέθηκε.', 404);
        }
        $form = new Dnna_Form_AutoForm('Application_Model_User', $this->view);
        $this->_helper->Get($this, $object, $form, 'user');
    }

    public function postAction() {
        throw new Exception('Not supported');
    }

    public function putAction() {
        throw new Exception('Not supported');
    }

    public function deleteAction() {
        throw new Exception('Not supported');
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Dnna_Form_AutoForm('Application_Model_User', $this->view), 'user');
    }
}
?>