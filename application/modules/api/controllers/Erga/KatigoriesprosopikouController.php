<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Erga_KatigoriesprosopikouController extends Api_IndexController
{
    const name = 'Κατηγορίες Προσωπικού (παράμετρος: <projectid>)';

    public function init() {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/erga/views/helpers', 'Erga_View_Helper');
    }
    
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $projectid = $this->getRequest()->getParam('projectid');
        if(!isset($projectid)) {
            throw new Exception('Η παράμετρος ?projectid είναι υποχρεωτική.');
        }
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($projectid);
        if(!isset($project)) {
            throw new Exception('Το έργο δεν βρέθηκε.');
        }
        if(!$auth->hasIdentity()) {
            throw new Exception('Δεν είστε συνδεδεμένος χρήστης.');
        } else if(!$auth->getStorage()->read()->hasRole('elke') && $project->get_basicdetails()->get_supervisor() !== $auth->getStorage()->read()) {
            throw new Exception('Δεν έχετε πρόσβαση στις κατηγορίες προσωπικού του συγκεκριμένου έργου.');
        } else {
            $categories = $project->get_personnelcategories();
        }
        $this->_helper->Index($this, $categories, 'personnelcategories', array('recordid' => 'get_recordid'));
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_PersonnelCategories_PersonnelCategory')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $this->view->project = $object->get_project();
        $this->_helper->Get($this, $object, $this->getPersonnelCategoryForm(), 'personnelcategory');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('professor')) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_PersonnelCategories_PersonnelCategory')->find($this->_request->getParam('id'));
        $this->_helper->PostOrPut($this, get_class($object), $this->getPersonnelCategoryForm());
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            throw new Exception('Access denied');
        }
        $object = Zend_Registry::get('entityManager')->getRepository('Erga_Model_PersonnelCategories_PersonnelCategory')->find($this->_request->getParam('id'));
        $this->_helper->PostOrPut($this, get_class($object), $this->getPersonnelCategoryForm(), $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Erga_Model_PersonnelCategories_PersonnelCategory', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, $this->getPersonnelCategoryForm(), 'personnelcategory');
    }
    
    protected function getPersonnelCategoryForm() {
        $form = new Dnna_Form_FormBase();
        $form->addElement( 'hidden', 'recordid', array(
            'label' =>  'Κωδικός Κατηγορίας Προσωπικού'
        ));
        $projectform = new Dnna_Form_SubFormBase();
        $projectform->addElement('text', 'projectid', array(
            'label' =>  'Κωδικός Έργου',
        ));
        $projectform->setLegend('Έργο');
        $form->addSubForm($projectform, 'project');
        $form->addElement('text', 'name', array(
            'label' =>  'Όνομα Κατηγορίας Προσωπικού',
        ));
        return $form;
    }
}
?>