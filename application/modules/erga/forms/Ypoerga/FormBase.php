<?php
abstract class Erga_Form_Ypoerga_FormBase extends Dnna_Form_FormBase {
    protected function addSubmitFields(&$subform = array()) {
        if(strpos($this->_view->getActionName(), 'new') !== false) {
            $this->addElement('submit', 'submitcontinue', array(
                'ignore' => true,
                'label' => 'Υποβολή και συνέχεια',
                'class' => 'submitcontinuebutton',
            ));
        }
        parent::addSubmitFields($subform);
    }
}
?>
