<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Employee extends Dnna_Form_SubFormBase {
    protected $_fromaitisi;

    public function __construct($view = null, $fromaitisi = false) {
        $this->_fromaitisi = $fromaitisi;
        parent::__construct($view);
    }

    public function init() {
        // Ονοματεπώνυμο
        $this->addElement('text', 'surname', array(
            'label' => 'Επώνυμο:',
            'required' => true,
        ));
        $this->addElement('text', 'firstname', array(
            'label' => 'Όνομα:',
            'required' => true,
        ));
        // Διεύθυνση Κατοικίας (οδός, αριθμός, ΤΚ, πόλη)
        $this->addElement('text', 'address', array(
            'label' => 'Διεύθυνση Κατοικίας (οδός, αριθμός, ΤΚ, πόλη):',
        ));
        // Email
        $this->addElement('text', 'email', array(
            'label' => 'Email:',
        ));
        // Τηλέφωνο
        $this->addElement('text', 'phone', array(
            'label' => 'Τηλέφωνο:',
        ));
        // Α.Δ.Τ.
        $this->addElement('text', 'adt', array(
            'label' => 'Α.Δ.Τ.:',
        ));
        // Α.Φ.Μ.
        $this->addElement('text', 'afm', array(
            'label' => 'Α.Φ.Μ.:',
            'required' => true,
        ));
        // Δ.Ο.Υ.
        $this->addElement('text', 'doy', array(
            'label' => 'Δ.Ο.Υ.:',
            'required' => true,
        ));
        if($this->_fromaitisi != true) {
            // LDAP Username
            $this->addElement('text', 'ldapusername', array(
                'label' => 'Όνομα Χρήστη LDAP:',
            ));
            
            // Μέγιστος αριθμός ωρών
            $this->addElement('text', 'maxhours', array(
                'label' => 'Μέγιστος Αριθμός Ωρών:',
                'validators' => array(
                    array('validator' => 'Int')
                ),
            ));
        }
    }
}
?>