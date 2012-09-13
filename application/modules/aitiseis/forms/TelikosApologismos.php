<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_TelikosApologismos extends Aitiseis_Form_Aitisi {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $subform->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => true), $this->_view), 'project', false);

        // Περιγραφή
        $subform->addElement('textarea', 'description', array(
            'label' => 'Περιγραφή:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
            'required' => true,
        ));
        // Δημοσιεύσεις σε επιστημονικά περιοδικά
        $subform->addElement('text', 'publications', array(
            'label' => 'Δημοσιεύσεις σε επιστημονικά περιοδικά:',
            'validators' => array(
                array('validator' => 'Int')
            ),
        ));
        // Ανακοινώσεις σε συνέδρια
        $subform->addElement('text', 'anakoinwseis', array(
            'label' => 'Ανακοινώσεις σε συνέδρια:',
            'validators' => array(
                array('validator' => 'Int')
            ),
        ));
        // Αναφορές σε citation index
        $subform->addElement('text', 'anafores', array(
            'label' => 'Αναφορές σε citation index:',
            'validators' => array(
                array('validator' => 'Int')
            ),
        ));
        // Άλλα
        $subform->addElement('textarea', 'alla', array(
            'label' => 'Άλλα(αναφέρετε):',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>