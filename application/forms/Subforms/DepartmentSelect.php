<?php
class Application_Form_Subforms_DepartmentSelect extends Dnna_Form_SubFormBase {
    protected $_label = 'Τμήμα';
    protected $_isrequired = true;

    public function __construct($label = null, $required = true) {
        $this->setLegend('Τμήμα');
        if($label != null) {
            $this->_label = $label;
        }
        if($required == true || $required == false) {
            $this->_isrequired = $required;
        }
        parent::__construct();
    }

    public function init() {
        // Στοιχεία Φορέα Χρηματοδότησης
        $departments = Zend_Registry::get('entityManager')->getRepository('Application_Model_Department')->findAll();
        $multioptions = array();
        foreach($departments as $curDepartment) {
            $multioptions[$curDepartment->get_id()] = $curDepartment->get_name();
        }
        $this->addElement('select', 'id', array(
            'label' => $this->_label.':',
            'required' => $this->_isrequired,
            'multiOptions' => $multioptions,
        ));
    }
}
?>