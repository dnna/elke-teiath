<?php
class Erga_Form_Ypoerga_ParadoteoImport extends Erga_Form_Ypoerga_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->setEnctype('multipart/form-data');
        // Excel
        $subform = new Dnna_Form_SubFormBase($this->view);
        $subform->addElement('file', 'file', array(
            'label' => 'Αρχείο Excel:',
            'validators' => array(
                array('validator' => 'Size', 'options' => array('10MB')),
                array('validator' => 'Count', 'options' => array(1)),
                array('validator' => 'Extension', 'options' => array('xls', 'xlsx')),
            ),
            'required' => true,
        ));
        $this->addSubForm($subform, 'default');
        $this->addSubmitFields();
    }
}
?>