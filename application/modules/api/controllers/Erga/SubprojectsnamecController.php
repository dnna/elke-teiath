<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_SubprojectsnamecController extends Api_IndexController
{
    const noindex = true;

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->_request->getParam('id'));
        $form = new Erga_Form_SubprojectsName($this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }
}
?>