<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_Ypoerga_ApasxoloumenoiController extends Api_IndexController
{
    const name = 'Απασχολούμενοι';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $subprojectid = $this->getRequest()->getParam('subprojectid');
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($subprojectid);
        if($subproject == null) {
            throw new Exception('Παρακαλώ επιλέξτε υποέργο μέσω της GET παραμέτρου ?subprojectid');
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if($auth->getStorage()->read()->hasRole('elke') || ($auth->getStorage()->read()->hasRole('professor') && $subproject->get_subprojectsupervisor() == $auth->getStorage()->read()->hasRole('professor'))) {
            $employees = $subproject->get_employees();
        } else {
            throw new Exception('Δεν έχετε δικαίωμα να δείτε τους απασχολούμενους για το συγκεκριμένο έργο.');
        }
        // Εύρεση των πεδίων
        $form = new Erga_Form_Apasxoloumenoi_Employee($this->view);
        $properties = array();
        foreach($form->getSubForm('default')->getElements() as $curName => $curElement) {
            $properties[$curName] = 'get_'.$curName;
        }
        unset($properties['recordid']);

        $this->_helper->Index($this, $employees, 'employees', array('recordid' => 'get_recordid'), $properties);
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = new Erga_Form_Apasxoloumenoi_Employee($this->view);
        $this->_helper->Get($this, $object, $form, 'employee');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        $this->view->subproject = $subproject;
        if($subproject == null) {
            throw new Exception("Παρακαλώ ορίστε το υποέργο στο οποίο θα ανήκει το παραδοτέο μέσω της παραμέτρου ?subprojectid στο query string.");
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Apasxoloumenoi_Employee($this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        $this->view->subproject = $subproject;
        if($subproject == null) {
            throw new Exception("Παρακαλώ ορίστε το υποέργο στο οποίο θα ανήκει το παραδοτέο μέσω της παραμέτρου ?subprojectid στο query string.");
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Apasxoloumenoi_Employee($this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Erga_Model_SubItems_SubProjectEmployee', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, new Erga_Form_Apasxoloumenoi_Employee($this->view), 'employee');
    }
}
?>