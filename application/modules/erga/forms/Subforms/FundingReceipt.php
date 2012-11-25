<?php
class Erga_Form_Subforms_FundingReceipt extends Dnna_Form_SubFormBase {
    protected $_i;
    
    public function __construct($i, $view = null) {
        $this->_i = $i;
        parent::__construct($view);
    }
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Φορέας Χρηματοδότησης
        $project = $this->_view->getProject();
        $fundingagencysubform = new Dnna_Form_SubFormBase();
        $fundingagencysubform->addElement('select', 'recordid', array(
            'required' => true,
            'label' => 'Φορέας Χρηματοδότησης:',
            'multiOptions' => array('null' => '-')+$project->get_financialdetails()->get_fundingagenciesAsArray()
        ));
        $this->addSubForm($fundingagencysubform, 'fundingagency', false);
        // Ημερομηνία
        $this->addElement('text', 'date', array(
            'label' => 'Ημερομηνία:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        $this->addElement('text', 'amount', array(
            'label' => 'Ποσό:',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
    }
}
?>