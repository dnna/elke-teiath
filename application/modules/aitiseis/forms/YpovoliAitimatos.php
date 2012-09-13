<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_YpovoliAitimatos extends Aitiseis_Form_Aitisi {
    protected function addStoixeiaAitimatos(&$subform) {
        // Κωδικός Αίτησης
        $subform->addElement('hidden', 'aitisiid', array());
        $subform->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => false), $this->_view), 'project', false);
        // Θέμα
        $subform->addElement('textarea', 'title', array(
            'label' => 'Θέμα:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );
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
        // Συνημμένο
        $subform->addElement('hidden', 'attachmentname', array('label' => 'Όνομα Συνημμένου:'));
        $subform->addElement('file', 'attachment', array(
            'label' => 'Συνημμένο:',
            'validators' => array(
                array('validator' => 'Size', 'options' => array('10MB')),
                array('validator' => 'Count', 'options' => array(1)),
                //array('validator' => 'Extension', 'options' => array('xls', 'xlsx')),
            ),
        ));
        // Επείγον
        $subform->addElement('checkbox', 'urgent', array(
            'label' => 'Επείγον:',
        ));
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addStoixeiaAitimatos($subform);
        $subform->setLegend('Αίτημα προς ΕΕΕ');
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
    
    public function isValid($data) {
        // Workaround για το ini size exceeded error όταν δεν στέλνεται καθόλου attachment field από τον client
        if(isset($data['default']['attachmentname']) && $data['default']['attachmentname'] != "" &&  !isset($data['default']['attachment'])) {
            $this->getSubForm('default')->removeElement('attachment');
        }
        return parent::isValid($data);
    }
}

?>