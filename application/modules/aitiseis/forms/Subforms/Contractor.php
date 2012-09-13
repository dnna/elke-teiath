<?php
class Aitiseis_Form_Subforms_Contractor extends Dnna_Form_SubFormBase {
    
    public function __construct() {
        $this->setLegend('Στοιχεία Ανάδοχου Φορέα');
        parent::__construct();
    }
    
    public function init() {
        // Επωνυμία
        $this->addElement('hidden', 'id', array(
        ));
        // Επωνυμία
        $this->addElement('text', 'name', array(
            'label' => 'Επωνυμία:',
        ));
        // ΑΦΜ
        $this->addElement('text', 'afm', array(
            'label' => 'ΑΦΜ:',
        ));        
        // ΔΟΥ
        $this->addElement('text', 'doy', array(
            'label' => 'ΔΟΥ:',
        ));        
        // Διεύθυνση
        $this->addElement('text', 'address', array(
            'label' => 'Διεύθυνση:',
        ));
        // Υπεύθυνος Επικοινωνίας
        $this->addElement('text', 'contact', array(
            'label' => 'Υπεύθυνος Επικοινωνίας:',
        ));
        // Τηλέφωνο
        $this->addElement('text', 'phone', array(
            'label' => 'Τηλέφωνο:',
        ));
        // Email
        $this->addElement('text', 'email', array(
            'label' => 'Email:',
        ));
    }
}
?>
