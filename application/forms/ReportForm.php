<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_ReportForm extends Dnna_Form_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $subform = new Dnna_Form_SubFormBase();

        // Περιγραφή
        $subform->addElement('textarea', 'description', array(
            'label' => 'Σύντομη Περιγραφή:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
        $this->addSubForm($subform, 'default');

        $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Υποβολή',
        ));
    }
}
?>