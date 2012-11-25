<?php
class  Erga_Form_BasicDetails extends Dnna_Form_SubFormBase {
    protected $_project;

    public function __construct($view = null, $project = null) {
        $this->_project = $project;
        parent::__construct($view);
    }

    protected function addProjectBasicFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
            $subform->setLegend('Βασικά Στοιχεία Έργου');
        }
        // Κατηγορία
        $projectcategorysubform = new Dnna_Form_SubFormBase();
        $projectcategorysubform->addElement('select', 'id', array(
            'required' => true,
            'label' => 'Κατηγορία:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ProjectCategory')
        ));
        $subform->addSubForm($projectcategorysubform, 'category', false);
        // MIS
        $subform->addElement('text', 'mis', array(
            'label' => 'Κωδικός έργου (MIS):',
            'allowempty' => false,
            )
        );
        $subform->getElement('mis')->addValidator(new Application_Form_Validate_AtLeastOneNotEmpty(array('mis', 'acccode')));
        // Κωδικός Λογιστηρίου
        $subform->addElement('text', 'acccode', array(
            'label' => 'Κωδικός Λογιστηρίου:',
            )
        );
        // Τίτλος έργου
        $subform->addElement('textarea', 'title', array(
            'label' => 'Τίτλος 1:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );
        // Τίτλος έργου (Αγγλικά)
        $subform->addElement('textarea', 'titleen', array(
            'label' => 'Τίτλος 2:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            )
        );
        // Περιγραφή
        $subform->addElement('textarea', 'description', array(
            'label' => 'Σύντομη Περιγραφή:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
           // 'required' => true,
        ));
        // Ημερομηνία Έναρξης
        $subform->addElement('text', 'startdate', array(
            'label' => 'Ημερομηνία έναρξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Ημερομηνία Λήξης (ίδια γραμμή)
        $subform->addElement('text', 'enddate', array(
            'label' => 'Ημερομηνία λήξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Απόφαση Έγκρισης ΕΕΕ
        $subform->addElement('text', 'refnumapproved', array(
            'label' => 'Απόφαση Έγκρισης ΕΕΕ',
            )
        );
        // Αρ. Πρωτ. Ένταξης
        $subform->addElement('text', 'refnumstart', array(
            'label' => 'Απόφαση ένταξης',
            )
        );

        if($createSubform) {
            $this->addSubForm($subform, 'default');
        }
    }

    protected function addCommitteeSelectFields(&$subform = null) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase(array('legend' => 'Στοιχεία Επιστημονικά Υπεύθυνου'));
        }

        // Αντικείμενα 1-10
        for($i = 1; $i <= 10; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_CommitteeMember($i), $i, null, 'basicdetails-committee');
        }
        
        $subform->addElement('button', 'addCommitteeMember', array(
            'label' => 'Προσθήκη Νέου Μέλους',
            'class' => 'committeebuttons addButton',
        ));
        $this->addSubForm($subform, 'committee');
        $this->getSubForm('committee')->setLegend('Επιστημονική Επιτροπή Έργου');
    }

    protected function addPreviousSupervisorsSelectFields(&$subform = null) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase(array('legend' => 'Προηγούμενοι Επιστημονικά Υπεύθυνοι'));
        }

        // Αντικείμενα 1-10
        for($i = 1; $i <= 10; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_PreviousSupervisor($i), $i, null, 'basicdetails-previoussupervisors');
        }

        $subform->addElement('button', 'addPreviousSupervisor', array(
            'label' => 'Προσθήκη Νέου',
            'class' => 'previoussupervisorsbuttons addButton',
        ));
        $this->addSubForm($subform, 'previoussupervisors');
        $this->getSubForm('previoussupervisors')->setLegend('Προηγούμενοι Επιστημονικά Υπεύθυνοι');
    }

    protected function addModificationFields(&$subform = null) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }
        // Αντικείμενα 1-20
        for($i = 1; $i <= 20; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_Modification($i), $i, null, 'basicdetails-modifications');
        }

        $subform->addElement('button', 'addModification', array(
            'label' => 'Προσθήκη Τροποποίησης',
            'class' => 'modificationbuttons addButton',
        ));
        $this->addSubForm($subform, 'modifications');
        $this->getSubForm('modifications')->setLegend('Τροποποιήσεις Απόφασης Ένταξης');
    }

    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/projectbasicdetails.js', 'text/javascript'));
        // basicdetailsid
        if($this->getElement('basicdetailsid') == null) {
            $this->addElement('hidden', 'basicdetailsid', array(
                'readonly' => true,
                )
            );
        }

        $this->addSubForm(new Application_Form_Subforms_DepartmentSelect('Τμήμα'), 'department');

        $this->addSubForm(new Application_Form_Subforms_SupervisorSelect(null, $this->_view), 'supervisor');
        $this->getSubForm('supervisor')->setLegend('Στοιχεία Επιστημονικά Υπεύθυνου');
        $this->addExpandImg('supervisor');

        $this->addPreviousSupervisorsSelectFields();

        $this->addCommitteeSelectFields();

        $this->addProjectBasicFields();

        $this->addModificationFields();
    }
}
?>