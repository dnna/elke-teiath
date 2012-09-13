<?php
class Synedriaseisee_Form_Subject extends Dnna_Form_SubFormBase {
    protected $_i;
    protected $_showSynedriasiId = false;
    
    public function __construct($i, $view = null) {
        if($i > 0) {
            $this->_i = $i;
        } else {
            $this->_i = 1;
            $this->_showSynedriasiId = true;
        }
        parent::__construct($view);
    }
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Συνεδρίαση
        if($this->_showSynedriasiId == true) {
            $synedriasisubform = new Dnna_Form_SubFormBase($this->_view);
            $synedriasisubform->addElement('hidden', 'id', array(
                'value' => 'null',
            ));
            $this->addSubForm($synedriasisubform, 'synedriasi', false);
        }
        // Αριθμός Θέματος Συνεδρίασης
        $this->addElement('hidden', 'num', array(
            'value' => $this->_i
        ));
        // Τίτλος
        $this->addElement('text', 'title', array(
            'label' => 'Θέμα '.$this->_i,
        ));
        // Αίτηση
        $aitisisubform = new Dnna_Form_SubFormBase($this->_view);
        $aitisisubform->addElement('hidden', 'aitisiid', array(
            'value' => 'null',
        ));
        $aitisisubform->addElement('hidden', 'aitisiname', array(
            'value' => 'null',
            'ignore' => true,
        ));
        $this->addSubForm($aitisisubform, 'aitisi', false);
    }
}
?>