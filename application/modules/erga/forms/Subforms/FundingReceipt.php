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