<?php
class Erga_Form_Subforms_Coordinator extends Dnna_Form_SubFormBase {
    public function init() {
        $this->addElement('text', 'partnercontact', array(
            'label' => 'Υπεύθυνος Επικοινωνίας:',
            'required' => true,
        ));
        $this->addElement('text', 'partnerphone', array(
            'label' => 'Τηλέφωνο/Fax:',
            'required' => true,
        ));
        $this->addElement('text', 'partneremail', array(
            'label' => 'email:',
            'required' => true,
        ));
    }
}
?>