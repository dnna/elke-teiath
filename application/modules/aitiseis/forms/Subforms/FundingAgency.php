<?php
class Aitiseis_Form_Subforms_FundingAgency extends Dnna_Form_SubFormBase {
    
    public function __construct() {
        $this->setLegend('Στοιχεία Φορέα Χρηματοδότησης');
        parent::__construct();
    }
    
    public function init() {
        // Στοιχεία Φορέα Χρηματοδότησης
        $this->addElement('select', 'id', array(
            'label' => 'Επωνυμία:',
            'required' => true,
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency'),
        ));
    }
}
?>
