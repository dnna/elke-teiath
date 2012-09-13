<?php
class Aitiseis_Form_Subforms_Deliverable extends Dnna_Form_SubFormBase {
    
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        $this->addSubForm(new Application_Form_Subforms_DeliverableSelect(array(), $this->_view), 'deliverable', false);
        $this->addElement('textarea', 'comments', array(
            'label' => 'Σχόλια:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
        ));
    }
}
?>