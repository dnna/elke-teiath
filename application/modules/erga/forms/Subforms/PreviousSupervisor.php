<?php
class Erga_Form_Subforms_PreviousSupervisor extends Dnna_Form_SubFormBase {
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
        $element->setLabel('Επιστημονικά Υπεύθυνος '.$this->_i.':');
        $subform->addElement($element);
        //$subform->getElement('userid')->setAllowEmpty(false);
        //$subform->getElement('userid')->addValidator(new Application_Form_Validate_Supervisor(true));
        // Ονοματεπώνυμο
        $subform->addElement('hidden', 'realname', array(
            'ignore' => true,
            )
        );
        $this->addSubForm($subform, 'user', false);

        // Ημερομηνία Έναρξης
        $this->addElement('text', 'startdate', array(
            'label' => 'Ημερομηνία έναρξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Ημερομηνία Λήξης (ίδια γραμμή)
        $this->addElement('text', 'enddate', array(
            'label' => 'Ημερομηνία λήξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
    }
}
?>