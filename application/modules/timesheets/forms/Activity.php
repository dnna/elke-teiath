<?php
class  Timesheets_Form_Activity extends Dnna_Form_SubFormBase {
    public function init() {
		$validate = function($value) {
			$parts = explode(':', $value);
			foreach($parts as $curPart) {
				if(!is_numeric($curPart) || ((int)$curPart) < 0 || ((int)$curPart) >= 24) {
					return false;
				}
			}
			return true;
		};

        $this->addElement('text', 'start', array(
            'required' => true,
            'validators' => array(
			new Zend_Validate_Callback($validate),
            //array('validator' => 'Digits'),
            //new Zend_Validate_Date(array("format" => 'G')),
            //new Zend_Validate_Between(array('min' => 1, 'max' => 24, 'inclusive' => true)),
            ),
        ));
        $this->addElement('text', 'end', array(
            'required' => true,
            'validators' => array(
			new Zend_Validate_Callback($validate),
            //array('validator' => 'Digits'),
            //new Zend_Validate_Date(array("format" => 'G')),
            //new Zend_Validate_Between(array('min' => 1, 'max' => 24, 'inclusive' => true)),
            ),
        ));
    }
}
?>