<?php
class Aitiseis_Form_Subforms_LoanItems_LoanItemEmployee extends Aitiseis_Form_Subforms_LoanItems_LoanItem {
    protected $_employee;

    public function __construct($employee, $view = null) {
        $this->_employee = $employee;
        parent::__construct($view);
    }

    public function getAttachedObject() {
        return $this->_employee;
    }

    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Budget Item
        $employeesubform = new Dnna_Form_SubFormBase();
        $employeesubform->addElement('hidden', 'recordid', array(
            'required' => $this->_required,
            'value' => $this->_employee->get_recordid(),
            'readonly' => true,
        ));
        $this->addSubForm($employeesubform, 'employee', false);
        $this->addElement('text', 'amount', array(
            'required' => $this->_required,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'label' => $this->_employee->__toString().' ('.$this->_employee->get_amount()."€".') :',
            'class' => 'formatFloat loanEmployeeCalcSum',
        ));
    }
}
?>