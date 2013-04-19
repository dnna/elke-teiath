<?php
class Erga_Form_Ypoerga_Paradoteo extends Erga_Form_Ypoerga_FormBase {
    /**
     * @var Erga_Model_SubItems_Deliverable
     */
    protected $_deliverable;
    
    public function __construct($view = null, $deliverable = null) {
        $this->_deliverable = $deliverable;
        parent::__construct($view);
    }

    protected function addAuthorFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
            $subform->setLegend('Συντάκτες');
        }
        // Αντικείμενα 1-30
        for($i = 1; $i <= 30; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_Author($i, $this->_view), $i, null, 'default-authors');
            //$subform->getSubForm($i)->setLegend('Συντάκτης '.$i);
        }
        $subform->addElement('button', 'addAuthor', array(
            'label' => 'Προσθήκη Συντάκτη',
            'class' => 'authorbuttons addButton',
        ));
        if($createSubform == true) {
            $this->addSubForm($subform, 'authors');
        }
    }
    
    protected function addContractorFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
            $subform->setLegend('Ανάδοχος');
        }
        $subproject = $this->_view->getSubProject();
        $subform->addElement('select', 'recordid', array(
            'label' => 'Ανάδοχος:',
            'multiOptions' => $subproject->get_contractorsAs2dArray()
        ));
        if($createSubform == true) {
            $this->addSubForm($subform, 'contractor');
        }
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/paketaergasias/paradotea.js', 'text/javascript'));

        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Στοιχεία Παραδοτέου');
        // Recordid
        $subform->addElement('hidden', 'recordid', array());
        // Κωδικός Παραδοτέου
        $subform->addElement('text', 'codename', array(
            'label' => 'Κωδικός Παραδοτέου:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 10))
            ),
            'required' => true,
            'placeholder' => 'πχ. Π.1.1',
            )
        );
        // Τίτλος Παραδοτέου
        $subform->addElement('textarea', 'title', array(
            'label' => 'Τίτλος Παραδοτέου:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );
        // Ποσό
        $subform->addElement('text', 'amount', array(
            'label' => 'Ποσό:',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
            'required' => true,
        ));
        // Όρια ανα Κατηγορία Προσωπικού
        $project = $this->_view->getProject();
        if(isset($project) && $project->get_personnelcategories()->count() > 0) {
            $limitsform = new Dnna_Form_SubFormBase($this->_view);
            $i = 1;
            foreach($project->get_personnelcategories() as $curCategory) {
                $curlimitform = new Dnna_Form_SubFormBase($this->_view);
                // Recordid
                $curlimitform->addElement('hidden', 'recordid', array());
                // Id κατηγορίας
                $curlimitcategoryform = new Dnna_Form_SubFormBase($this->_view);
                $curlimitcategoryform->addElement('hidden', 'recordid', array(
                    'value' =>  $curCategory->get_recordid(),
                ));
                $curlimitform->addSubForm($curlimitcategoryform, 'personnelcategory', false);
                // Limit
                $curlimitform->addElement('text', 'limit', array(
                    'label' => 'Όριο ωρών για '.$curCategory->get_name().':',
                    'validators' => array(
                        array('validator' => 'Integer')
                    ),
                    'required' => false,
                ));
                $curlimitform->set_empty(false);
                $limitsform->addSubForm($curlimitform, $i, false, 'default-limits');
                $i++;
            }
            $subform->addSubForm($limitsform, 'limits', false);
        }
        // Έναρξη
        $subform->addElement('text', 'startdate', array(
            'label' => 'Ημερομηνία Έναρξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
                    'required' => false,
        ));
        // Λήξη
        $subform->addElement('text', 'enddate', array(
            'label' => 'Ημερομηνία Λήξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
                    'required' => false,
        ));
        // Εγκριση αναθεσης απο επιτροπη ερευνων
        $subform->addElement('text', 'assignmentapprovaldate', array(
            'label' => 'Έγκριση Ανάθεσης από Επιτροπή Ερευνών:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Εγκριση ολοκλήρωσης απο Επιτροπή Ερευνών
        $subform->addElement('text', 'completionapprovaldate', array(
            'label' => 'Εγκριση Ολοκλήρωσης απο Επιτροπή Ερευνών:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));

        // Σχόλια
        $subform->addElement('textarea', 'comments', array(
            'label' => 'Γενικές Πληροφορίες:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));

        $subsubform = new Dnna_Form_SubFormBase();
        if($this->_view->getSubProject()->get_subprojectdirectlabor() == "1") {
            $subsubform->setLegend('Συντάκτες');
            if(($this->_view->getSubProject()->get_employees() != null && count($this->_view->getSubProject()->get_employees()) > 0) ||
                    ($this->_view->getProject() != null && $this->_view->getProject()->get_thisprojectemployees() != null && count($this->_view->getProject()->get_thisprojectemployees()) > 0)) {
                $this->addAuthorFields($subsubform, false);
            } else {
                $element = new Application_Form_Element_Note('noauthorsnote', array(
                    'value' => 'Δεν έχουν οριστεί απασχολούμενοι'
                ));
                $subsubform->addElement($element);
            }
            $subform->addSubForm($subsubform, 'authors');
        } else {
            $subsubform->setLegend('Ανάδοχος');
            if($this->_view->getSubProject()->get_contractors() != null && $this->_view->getSubProject()->get_contractors()->count() > 0) {
                $this->addContractorFields($subsubform, false);
            } else {
                $element = new Application_Form_Element_Note('noauthorsnote', array(
                    'value' => 'Δεν έχουν οριστεί ανάδοχοι'
                ));
                $subsubform->addElement($element);
            }
            $subform->addSubForm($subsubform, 'contractor');
        }
        $this->addSubForm($subform, 'default');
        $this->addSubmitFields();
    }

    public function isValid($data) {
        if(isset($data['default']['limits'])) {
            for($i = 1; $i <= count($data['default']['limits']); $i++) {
                $data['default']['limits'][$i]['isvisible'] = '1';
            }
        }
        return parent::isValid($data);
    }
}
?>