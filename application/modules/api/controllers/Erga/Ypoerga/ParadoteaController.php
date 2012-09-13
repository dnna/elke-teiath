<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_Ypoerga_ParadoteaController extends Api_IndexController
{
    const name = 'Παραδοτέα (παράμετροι: <authorid>|<contractorid>|<afm>,[subprojectid])';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $name = $this->utf8_urldecode($this->getRequest()->getParam('name'));
        $afm = $this->getRequest()->getParam('afm');
        $authorid = $this->getRequest()->getParam('authorid');
        $contractorid = $this->getRequest()->getParam('contractorid');
        $subprojectid = $this->getRequest()->getParam('subprojectid');
        if($name == null) {
            $name = $this->utf8_urldecode($this->getRequest()->getParam('q'));
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if(!$auth->getStorage()->read()->hasRole('elke')) {
            $deliverables = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->findDeliverables(array(
                'supervisoruserid' => $auth->getStorage()->read()->get_userid(),
                'search' => $name,
                'subprojectid' => $subprojectid,
                'authorid' => $authorid,
                'contractorid' => $contractorid,
                'afm' => $afm,
            ));
        } else {
            $deliverables = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->findDeliverables(array(
                'search' => $name,
                'subprojectid' => $subprojectid,
                'authorid' => $authorid,
                'contractorid' => $contractorid,
                'afm' => $afm,
            ));
        }
        
        // Εύρεση των πεδίων
        $this->view->subproject = new Erga_Model_SubProject();
        $this->view->subproject->set_subprojectdirectlabor(1);
        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
        $properties = array();
        foreach($form->getSubForm('default')->getElements() as $curName => $curElement) {
            $properties[$curName] = 'get_'.$curName;
        }
        unset($properties['recordid']);

        $this->_helper->Index($this, $deliverables, 'deliverables', array('recordid' => 'get_recordid'), $properties);
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $this->view->deliverable = $object;
        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
        $this->_helper->Get($this, $object, $form, 'deliverable');
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
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
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
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->find($this->_request->getParam('id'));
        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
        $this->_helper->PostOrPut($this, get_class($object), $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Erga_Model_SubItems_Deliverable', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        $type = $this->getRequest()->getParam('type', null);
        if(!isset($type) || ($type !== 'autepistasia' && $type !== 'diagonismos')) {
            throw new Exception('Το schema εξαρτάται από το αν το υποέργο στο οποίο ανήκει είναι αυτεπιστασία ή διαγωνισμός. Παρακαλώ ορίστε την παράμετρο ?type στο query string με τιμές "autepistasia" ή "diagonismos".');
        } else if($type !== 'autepistasia') {
            $this->view->subproject = new Erga_Model_SubProject();
            $this->view->subproject->set_subprojectdirectlabor(1);
        } else {
            $this->view->subproject = new Erga_Model_SubProject();
            $this->view->subproject->set_subprojectdirectlabor(0);
        }
        echo $this->_helper->generateXsd($this, new Erga_Form_Ypoerga_Paradoteo($this->view), 'deliverable');
    }
}
?>