<?php
class Erga_Form_Subforms_BudgetItem extends Dnna_Form_SubFormBase {
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Category
        $subform = new Dnna_Form_SubFormBase();
        $subform->addElement('select', 'id', array(
            'required' => $this->_required,
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ExpenditureCategory')
        ));
        $this->addSubForm($subform, 'category', false);
        $this->getSubForm('category')->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'tableSimLeft'));
        $this->addElement('text', 'amount', array(
            'required' => $this->_required,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat calcSum',
        ));
        $this->getElement('amount')->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'tableSimRight'));
    }
}
?>