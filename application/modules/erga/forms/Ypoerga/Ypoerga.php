<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Ypoerga_Ypoerga extends Erga_Form_Ypoerga_FormBase {
    protected $_defaultSupervisor;
    /**
     * @var Erga_Model_SubProject
     */
    protected $_subproject;

    protected function addSubProjectFields(&$subform = null, $createSubform = true) {
        if($this->_view->getProject() == null) {
            $this->_view->project = new Erga_Model_Project();
        }
        $subprojectnames = $this->_view->getProject()->get_subprojectsname();
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
            $subform->setLegend('Στοιχεία '.$subprojectnames['gen']);
        }

        $subform->addElement('text', 'subprojectnumber', array(
            'label' => 'Αριθμός '.$subprojectnames['gen'].':',
            'required' => true,
            'validators' => array(
            array('validator' => 'GreaterThan', 'options' => array('min' => 0))
            ),
        ));
        // Τίτλος
        $subform->addElement('textarea', 'subprojecttitle', array(
            'label' => 'Τίτλος 1:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );

        $subform->getElement('subprojecttitle')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="clearBoth"></div>', 'placement' => 'append'));

        // Τίτλος (Αγγλικά)
        $subform->addElement('textarea', 'subprojecttitleen', array(
            'label' => 'Τίτλος 2:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            )
        );

        $subform->getElement('subprojecttitleen')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="clearBoth"></div>', 'placement' => 'append'));

        // Περιγραφή
        $subform->addElement('textarea', 'subprojectdescription', array(
            'label' => 'Σύντομη Περιγραφή:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
           // 'required' => true,
        ));
        // Προϋπολογισμός Έργου
        $subform->addElement('text', 'subprojectbudget', array(
            'label' => 'Προϋπολογισμός '.$subprojectnames['gen'].':',
            'required' => true,
            'validators' => array(
            array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        $subform->getElement('subprojectbudget')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="budgetfields">', 'placement' => 'prepend'));
        // ΦΠΑ Προϋπολογισμού Έργου
        $subform->addElement('text', 'subprojectbudgetfpa', array(
            'label' => 'ΦΠΑ:',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        // Σύνολο
        $subform->addElement('text', 'subprojectbudgetwithfpa', array(
            'label' => 'Σύνολο (με ΦΠΑ):',
            'readonly' => true,
            'ignore' => true,
            'class' => 'formatFloat',
        ));
        $subform->getElement('subprojectbudgetwithfpa')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '</div><div class="clearBoth"></div>', 'placement' => 'append'));
        // Ημερομηνία Έναρξης
        $subform->addElement('text', 'subprojectstartdate', array(
            'label' => 'Ημερομηνία έναρξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Ημερομηνία Λήξης (ίδια γραμμή)
        $subform->addElement('text', 'subprojectenddate', array(
            'label' => 'Ημερομηνία λήξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Τύπος
        $subform->addElement('select', 'subprojecttype', array(
            'required' => true,
            'label' => 'Τύπος:',
            'multiOptions' => Array(
                Erga_Model_SubProject::TYPE_MELETI => 'Μελέτη',
                Erga_Model_SubProject::TYPE_PROION => 'Προϊόν',
                Erga_Model_SubProject::TYPE_YPHRESIA => 'Υπηρεσία',
                )
        ));
        // Αυτεπιστασία
        $subform->addElement('select', 'subprojectdirectlabor', array(
            'required' => true,
            'label' => 'Αυτεπιστασία:',
            'multiOptions' => Array(
                '1' => 'Ναί',
                '0' => 'Όχι - Διαγωνισμός',
                )
        ));
        // Διαγωνισμός
        $subform->addSubForm(new Praktika_Form_Competition_Dates(), 'competition');
        $subform->getSubForm('competition')->setLegend('Στοιχεία Διαγωνισμού');

        if($createSubform) {
            $this->addSubForm($subform, 'default');
        }

        // Σχόλια
        $subform->addElement('textarea', 'comments', array(
            'label' => 'Γενικές Πληροφορίες:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
    }

    public function __construct($_defaultSupervisor = null, $view = null, $subproject = null) {
        $this->_defaultSupervisor = $_defaultSupervisor;
        $this->_subproject = $subproject;
        parent::__construct($view);
    }

    protected function addSubmitFields(&$subform = Array()) {
        // Project ID αν δεν υπάρχει
        if($this->getElement('subprojectid') == null) {
            $subprojectid = Zend_Controller_Front::getInstance()->getRequest()->getParam('subprojectid');
            $this->addElement('hidden', 'subprojectid', array(
                'value' => $subprojectid,
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
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/ypoerga/ypoerga.js', 'text/javascript'));

        // Επιστημονικά Υπεύθυνος
        $this->addSubForm(new Application_Form_Subforms_SupervisorSelect($this->_defaultSupervisor, $this->_view), 'subprojectsupervisor');
        $this->getSubForm('subprojectsupervisor')->setLegend('Στοιχεία Επιστημονικά Υπεύθυνου');
        $this->addExpandImg('subprojectsupervisor');

        $this->addSubProjectFields();

        //$this->addContractFields($dg);

        $this->addSubmitFields();
    }

    public function isValid($data) {
        if($data['default']['subprojectdirectlabor'] == '1') {
            unset($data['default']['competition']['recordid']);
        }
        return parent::isValid($data);
    }
}

?>