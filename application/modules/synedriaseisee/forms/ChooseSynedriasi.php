<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_Form_ChooseSynedriasi extends Dnna_Form_FormBase {
    public function init() {
        $this->addSubForm(new Application_Form_Subforms_SynedriasiSelect(array('required' => true), $this->_view), 'default', false);
        $this->addElement('text', 'num', array(
            'label' => 'Αρ. Θέματος Συνεδρίασης',
            'value' => '1',
            'required' => true
        ));
    }
}
?>