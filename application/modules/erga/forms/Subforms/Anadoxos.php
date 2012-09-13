<?php
class Erga_Form_Subforms_Anadoxos extends Dnna_Form_SubFormBase {
    
    public function __construct() {
        $this->setLegend('Στοιχεία Ανάδοχου Φορέα');
        parent::__construct();
    }
    
    public function init() {
        // Επωνυμία
        $this->addElement('select', 'id', array(
            'label' => 'Επωνυμία:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency')
        ));
    }
}
?>