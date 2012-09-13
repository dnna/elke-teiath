<?php
class  Erga_Form_Project extends Dnna_Form_FormBase {
    protected $_section;
    /**
     * @var Erga_Model_Project
     */
    protected $_project;
    
    public function __construct($section, $view, $project = null) {
        $this->_section = $section;
        $this->_project = $project;
        parent::__construct($view);
    }
    
    protected function addIsComplex(&$subform = Array()) {
        // Σύνθετο Έργο
        if(isset($this->_project)) {
            $subprojectnames = $this->_project->get_subprojectsname();
        } else {
            $project = new Erga_Model_Project();
            $subprojectnames = $project->get_subprojectsname();
        }
        if(isset($this->_project) && $this->_project->containsAitisiType('Aitiseis_Model_OnomastikiKatastasi')) {
            $subform->addElement('hidden', 'iscomplex', array(
                'required' => true,
                'value' => 0
            ));
            $subform->addElement('select', 'iscomplexprop', array(
                'required' => true,
                'label' => 'Έχει '.$subprojectnames['namepl'].':',
                'ignore' => true,
                'disabled' => true,
                'readonly' => true,
                'multiOptions' => Array('0' => 'Όχι - Συνδεδεμένη Αίτηση')
            ));
        } else {
            $subform->addElement('select', 'iscomplex', array(
                'required' => true,
                'label' => 'Έχει '.$subprojectnames['namepl'].':',
                'multiOptions' => Array('0' => 'Όχι', '1' => 'Ναί')
            ));
        }
    }
    
    protected function addSubmitFields(&$subform = Array()) {
        // Project ID αν δεν υπάρχει
        if($this->getElement('projectid') == null) {
            $projectid = Zend_Controller_Front::getInstance()->getRequest()->getParam('projectid');
            $this->addElement('hidden', 'projectid', array(
                'value' => $projectid,
                'readonly' => true,
                )
            );
        }
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Υποβολή',
            'class' => 'addbutton',
        ));
    }
    
    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        
        if(!isset($this->_section) || $this->_section === 'basicdetails') {
            // Τύπος έργου (απλό ή σύνθετο)
            $subform = new Dnna_Form_SubFormBase($this->_view);
            $this->addIsComplex($subform);
            $this->addSubForm($subform, 'default');
            // Τέλος τύπου έργου
            $this->addSubForm(new Erga_Form_BasicDetails($this->_view, $this->_project), 'basicdetails', false);
            $this->addSubForm(new Erga_Form_Position($this->_view, $this->_project), 'position', false);
        }
        if(!isset($this->_section) || $this->_section === 'financialdetails') {
            $this->addSubForm(new Erga_Form_FinancialDetails($this->_view, $this->_project), 'financialdetails', false);
        }
        if(!isset($this->_section) || $this->_section === 'position') {
            // Ignore
        }
        if(!isset($this->_section) || $this->_section === 'aitiseis') {
            $this->addSubForm(new Erga_Form_Aitiseis($this->_view, $this->_project), 'default', false);
        }
        
        $this->addSubmitFields();
    }
}
?>