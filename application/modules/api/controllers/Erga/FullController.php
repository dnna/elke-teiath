<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_FullController extends Api_IndexController
{
    const noindex = true;

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }
    
    public function indexAction() {
        throw new Exception('Για index των έργων χρησιμοποιήστε το resource "/api/erga"');
    }

    public function getAction() {
        $this->view->project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->_request->getParam('id'));
        if(!isset($this->view->project)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = new Erga_Form_FullProject($this->view->project, $this->view);
        $this->_helper->Get($this, $this->view->project, $form, 'project');
    }

    public function postAction() {
        throw new Exception('Not implemented');
    }

    public function putAction() {
        throw new Exception('Not implemented');
    }

    public function deleteAction() {
        throw new Exception('Not implemented');
    }

    public function schemaAction() {
        throw new Exception('Το schema για το συγκεκριμένο resource δεν είναι διαθέσιμο.');
    }
}
?>