<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_IndexController extends Zend_Controller_Action {
    public function indexAction() {
        $this->view->pageTitle = 'Αιτήσεις';
        $this->_request->setParam('format', null);
        if($this->_request->getUserParam('type') != null) {
            $this->_helper->redirector('index', 'view', 'aitiseis', $this->_request->getUserParams());
        }
        $this->view->aitiseistypes = $this->_helper->getAitiseisTypes();
    }
}
?>