<?php
class  Timesheets_Form_Timesheet extends Dnna_Form_SubFormBase {
    public function init() {
        $this->addElement('text', 'recordid', array(
            'required' => true,
            'validators' => array(
            array('validator' => 'Alnum')
            ),
        ));
        $this->addElement('text', 'mis', array(
            'validators' => array(
            array('validator' => 'Alnum')
            ),
        ));
        $this->addElement('text', 'year', array(
            'required' => true,
            'validators' => array(
            array('validator' => 'Digits')
            ),
        ));
        $this->addElement('text', 'month', array(
            'required' => true,
            'validators' => array(
            array('validator' => 'Digits')
            ),
        ));
    }
}
?>