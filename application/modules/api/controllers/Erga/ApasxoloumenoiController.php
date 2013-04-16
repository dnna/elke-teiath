<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_ApasxoloumenoiController extends Api_IndexController
{
    const name = 'Λίστα Απασχολούμενων Έργου';
    const noindex = true;

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $projectid = $this->getRequest()->getParam('projectid');
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($projectid);
        if($subproject == null) {
            throw new Exception('Παρακαλώ επιλέξτε έργο μέσω της GET παραμέτρου ?projectid');
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if($auth->getStorage()->read()->hasRole('elke') || ($auth->getStorage()->read()->hasRole('professor') && ($subproject instanceof Erga_Model_SubProject && $subproject->get_subprojectsupervisor() == $auth->getStorage()->read()->get_userid()) || ($subproject instanceof Erga_Model_Project && $subproject->get_basicdetails()->get_supervisor() == $auth->getStorage()->read()->get_userid()))) {
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
        $properties['afm'] = 'get_afm';
        unset($properties['recordid']);

        $this->_helper->Index($this, $employees, 'employees', array('recordid' => 'get_recordid'), array('subprojecttitle' => 'get_subproject')+$properties);
    }

    public function getAction() {
        $this->_helper->redirector->goToUrl($this->view->baseUrl('api/erga/ypoerga/apasxoloumenoi/').$this->_request->getParam('id'));
    }

    public function postAction() {
        throw new Exception('Δεν υποστηρίζεται. Οι απασχολούμενοι πρέπει να εισαχθούν μέσα από υποέργο.');
    }

    public function putAction() {
        throw new Exception('Δεν υποστηρίζεται. Οι απασχολούμενοι πρέπει να εισαχθούν μέσα από υποέργο.');
    }

    public function deleteAction() {
        throw new Exception('Δεν υποστηρίζεται. Οι απασχολούμενοι πρέπει να διαγραφούν μέσα από το σχετικό υποέργο.');
    }

    public function schemaAction() {
        $this->_helper->redirector->goToUrl($this->view->baseUrl('api/erga/ypoerga/apasxoloumenoi/schema'));
    }
}
?>