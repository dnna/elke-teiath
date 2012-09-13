<?php
class Aitiseis_Form_Subforms_Partner extends Dnna_Form_SubFormBase {
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        $subform = new Dnna_Form_SubFormBase();
        $subform->addElement('select', 'id', array(
            'label' => 'Συνεργαζόμενος Φορέας:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency'),
            'value' => '-',
        ));
        $this->addSubForm($subform, 'partnerlistitem', false);
    }
}
?>
