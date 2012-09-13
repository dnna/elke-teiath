<?php
class  Timesheets_Form_Activity extends Dnna_Form_SubFormBase {
    public function init() {
        $this->addElement('text', 'start', array(
            'required' => true,
            'validators' => array(
            array('validator' => 'Digits'),
            new Zend_Validate_Between(array('min' => 1, 'max' => 24, 'inclusive' => true)),
            ),
        ));
        $this->addElement('text', 'end', array(
            'required' => true,
            'validators' => array(
            array('validator' => 'Digits'),
            new Zend_Validate_Between(array('min' => 1, 'max' => 24, 'inclusive' => true)),
            ),
        ));
    }
}
?>