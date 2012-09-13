<?php
class Praktika_Form_Committee_Member extends Dnna_Form_SubFormBase {
    protected $_i;
    
    public function __construct($i, $view = null) {
        $this->_i = $i;
        parent::__construct($view);
    }

    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // User ID
        $subform = new Dnna_Form_SubFormBase();
        $element = new Application_Form_Element_Flexbox('userid');
        $element->setLabel('Μέλος '.$this->_i.':');
        $subform->addElement($element);
        //$subform->getElement('userid')->setAllowEmpty(false);
        //$subform->getElement('userid')->addValidator(new Application_Form_Validate_Supervisor(true));
        // Ονοματεπώνυμο
        $subform->addElement('hidden', 'realname', array(
            'ignore' => true,
            )
        );
        $this->addSubForm($subform, 'user', false);

        $this->addElement('select', 'capacity', array(
            'label' => 'Ιδιότητα:',
            'required' => true,
            'multiOptions' => Praktika_Model_Committee_Member::getCapacities()
        ));
    }

    public function isEmpty() {
        if($this->getElement('recordid')->getValue() != '') {
            return false;
        } else {
            return true;
        }
    }

    public static function addCommitteeMembers(&$form, $formname, $addsubform = true) {
        if($addsubform) {
            $subform = new Dnna_Form_SubFormBase($form->_view);
        } else {
            $subform = $form;
        }
        if($formname != null) {
            $newformname = $formname.'-committeemembers';
        } else {
            $newformname = 'committeemembers';
        }
        for($i = 1; $i <= 10; $i++) {
            $subform->addSubForm(new Praktika_Form_Committee_Member($i, $form->_view), $i, null, $newformname);
            $subform->getSubForm($i)->setLegend('Μέλος '.$i);
        }

        $subform->addElement('button', 'addMember', array(
            'label' => 'Προσθήκη Μέλους',
            'class' => 'memberbuttons addButton',
        ));
        if($addsubform) {
            $form->addSubForm($subform, 'committeemembers', false);
            // Id επιτροπής
            $form->addElement('hidden', 'id', array());
        }
    }
}
?>