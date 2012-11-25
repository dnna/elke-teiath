<?php
class Erga_Form_Subforms_FundingAgency extends Dnna_Form_SubFormBase {
    protected $_i;
    
    public function __construct($i, $view = null) {
        $this->_i = $i;
        parent::__construct($view);
    }
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Agency
        $agencyselectform = new Application_Form_Subforms_AgencySelect('Φορέας Χρηματοδότησης', true, $this->_view);
        $agencyselectform->setLegend(null);
        $this->addSubForm($agencyselectform, 'agency', false);
    }
}
?>