<?php
class Aitiseis_Form_Subforms_FundingAgencyMinimal extends Dnna_Form_SubFormBase {
    
    protected $_fieldtitle;
    
    public function __construct($fieldtitle, $options = null) {
        $this->_fieldtitle = $fieldtitle;
        parent::__construct($options);
    }
    
    public function init() {
        $this->addElement('select', 'id', array(
            'label' => $this->_fieldtitle.':',
            'required' => true,
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency'),
        ));
    }
}
?>