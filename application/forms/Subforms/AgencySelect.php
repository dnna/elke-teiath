<?php
class Application_Form_Subforms_AgencySelect extends Dnna_Form_SubFormBase {
    protected $_label = 'Επωνυμία';
    protected $_isrequired = true;

    public function __construct($label = null, $required = true) {
        $this->setLegend('Στοιχεία Φορέα Χρηματοδότησης');
        if($label != null) {
            $this->_label = $label;
        }
        if($required == true || $required == false) {
            $this->_isrequired = $required;
        }
        parent::__construct();
    }

    public function init() {
        // Στοιχεία Φορέα Χρηματοδότησης
        $agencies = Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency');
        $multioptions = array();
        if($this->_isrequired != true) {
            $multioptions['null'] = '-';
        }
        $multioptions = $multioptions + $agencies;
        $this->addElement('select', 'id', array(
            'label' => $this->_label.':',
            'required' => $this->_isrequired,
            'multiOptions' => $multioptions,
        ));
    }
}
?>