<?php
class Aitiseis_Form_Subforms_LoanItems_LoanItemContractor extends Aitiseis_Form_Subforms_LoanItems_LoanItem {
    protected $_contractor;

    public function __construct($contractor, $view = null) {
        $this->_contractor = $contractor;
        parent::__construct($view);
    }

    public function getAttachedObject() {
        return $this->_contractor;
    }

    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Budget Item
        $contractorsubform = new Dnna_Form_SubFormBase();
        $contractorsubform->addElement('hidden', 'recordid', array(
            'required' => $this->_required,
            'value' => $this->_contractor->get_recordid(),
            'readonly' => true,
        ));
        $this->addSubForm($contractorsubform, 'contractor', false);
        $this->addElement('text', 'amount', array(
            'required' => $this->_required,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'label' => $this->_contractor->__toString().' ('.$this->_contractor->get_amount()."€".') :',
            'class' => 'formatFloat calcSum',
        ));
    }
}
?>