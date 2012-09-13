<?php
class Aitiseis_Form_Subforms_Employee extends Dnna_Form_SubFormBase {
    protected $_i;
    protected $_fromaitisi;
    
    public function __construct($i, $view = null, $fromaitisi = false) {
        $this->_i = $i;
        $this->_fromaitisi = $fromaitisi;
        parent::__construct($view);
    }
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        if($this->_fromaitisi != true) {
        $this->addSubForm(new Application_Form_Employee($this->_view, $this->_fromaitisi), 'employee');
        $this->getSubForm('employee')->setLegend('Στοιχεία Απασχολούμενου');
        $this->getSubForm('employee')->setOrder(0);
        } else {
            $this->addSubForm(new Application_Form_Employee($this->_view, $this->_fromaitisi), 'employee', false);
        }
        // Ποσό Σύμβασης (με ΦΠΑ)
        $this->addElement('text', 'amount', array(
            'label' => 'Ποσό Σύμβασης (με ΦΠΑ):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        // Διάρκεια Σύμβασης (ΑΠΟ – ΕΩΣ)
        $this->addElement('text', 'startdate', array(
            'label' => 'Ημ/νία έναρξης σύμβασης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        $this->addElement('text', 'enddate', array(
            'label' => 'Ημ/νία λήξης σύμβασης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Ανθρωπομήνες
        $this->addElement('text', 'manmonths', array(
            'label' => 'Ανθρωπομήνες:',
            'validators' => array(
                array('validator' => 'Float')
            ),
        ));
        // Κατηγορία
        $catsubform = new Dnna_Form_SubFormBase($this->_view);
        $catsubform->addElement('select', 'id', array(
            'label' => 'Κατηγορία:',
            'value' => 'Α1',
            'multiOptions' => Application_Model_Repositories_EmployeeLists::getListAsArray('Application_Model_Lists_EmployeeCategory')
        ));
        $this->addSubForm($catsubform, 'category', false);
        // Ειδικότητα στο έργο
        $specsubform = new Dnna_Form_SubFormBase($this->_view);
        $specsubform->addElement('select', 'id', array(
            'label' => 'Ειδικότητα στο έργο:',
            'value' => 'Β1',
            'multiOptions' => Application_Model_Repositories_EmployeeLists::getListAsArray('Application_Model_Lists_EmployeeSpecialty')
        ));
        $this->addSubForm($specsubform, 'specialty', false);
        // Σχόλια
        $this->addElement('textarea', 'comments', array(
            'label' => 'Γενικές Πληροφορίες:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
    }
}
?>
