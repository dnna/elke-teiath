<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_Form_Synedriasi extends Dnna_Form_FormBase {
    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->addElement('select', 'start', array(
            'label' => 'Ώρα Έναρξης',
            'required' => true,
        ));
        $this->getElement('start')->setRegisterInArrayValidator(false);
        $this->addElement('select', 'end', array(
            'label' => 'Ώρα Λήξης',
            'registerInArrayValidator', false,
            'required' => true,
        ));
        $this->getElement('end')->setRegisterInArrayValidator(false);
        $this->addElement('text', 'num', array(
            'label' => 'Κωδ. Συνεδρίασης',
            'required' => true,
        ));

        $subform = new Dnna_Form_SubFormBase();
        // Αντικείμενα 1-20
        for($i = 1; $i <= 20; $i++) {
            $subform->addSubForm(new Synedriaseisee_Form_Subject($i, $this->_view), $i, null, 'subjects');
        }

        $subform->addElement('button', 'addSubject', array(
            'label' => 'Προσθήκη Θέματος',
            'class' => 'subjectbuttons addButton',
        ));
        $this->addSubForm($subform, 'subjects', false);
    }
}
?>