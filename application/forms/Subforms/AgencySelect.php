<?php
class Application_Form_Subforms_AgencySelect extends Dnna_Form_SubFormBase {
    protected $_label = 'Επωνυμία';
    protected $_isrequired = true;

    public function __construct($label = null, $required = true, $view = null) {
        $this->setLegend('Στοιχεία Φορέα Χρηματοδότησης');
        if($label != null) {
            $this->_label = $label;
        }
        if($required == true || $required == false) {
            $this->_isrequired = $required;
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();

        // Στοιχεία Φορέα Χρηματοδότησης
        /*$agencies = Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency');
        $multioptions = array();
        if($this->_isrequired != true) {
            $multioptions['null'] = '-';
        }
        $multioptions = $multioptions + $agencies;
        $this->addElement('select', 'id', array(
            'label' => $this->_label.':',
            'required' => $this->_isrequired,
            'multiOptions' => $multioptions,
        ));*/
        $element = new Application_Form_Element_Flexbox('id');
        $element->setLabel($this->_label);
        $element->setValue('null');
        $element->setRequired($this->_isrequired);
        $element->setAttrib('class', 'agencyselectid');
        $this->addElement($element);
        $this->addElement('hidden', 'name', array(
            'ignore' => true,
            'class' => 'agencyselectname',
        ));
    }
}
?>