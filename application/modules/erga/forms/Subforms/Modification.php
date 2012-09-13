<?php
class Erga_Form_Subforms_Modification extends Dnna_Form_SubFormBase {
    protected $_i;
    
    public function __construct($i, $view = null) {
        $this->_i = $i;
        parent::__construct($view);
    }
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Αρ. Πρωτ. Τροποποίησης
        $this->addElement('text', 'refnum', array(
            'label' => $this->_i.'η Τροποποίηση:',
        ));
    }
}
?>