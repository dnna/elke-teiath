<?php
class Aitiseis_Form_Subforms_LoanItems_LoanItemBudgetItem extends Aitiseis_Form_Subforms_LoanItems_LoanItem {
    protected $_budgetitem;

    public function __construct($budgetitem, $view = null) {
        $this->_budgetitem = $budgetitem;
        parent::__construct($view);
    }

    public function getAttachedObject() {
        return $this->_budgetitem;
    }

    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Budget Item
        $budgetitemsubform = new Dnna_Form_SubFormBase();
        $budgetitemsubform->addElement('hidden', 'recordid', array(
            'required' => $this->_required,
            'value' => $this->_budgetitem->get_recordid(),
            'readonly' => true,
        ));
        $this->addSubForm($budgetitemsubform, 'budgetitem', false);
        $this->addElement('text', 'amount', array(
            'required' => $this->_required,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'label' => $this->_budgetitem->__toString().' ('.$this->_budgetitem->get_amount()."€".') :',
            'class' => 'formatFloat calcSum',
        ));
    }
}
?>
