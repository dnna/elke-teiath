<?php
class Timesheets_Form_TimesheetFilters extends Dnna_Form_FormBase {
    public function __construct($view = null) {
        parent::__construct($view);
    }
    
    public function init() {
        $this->setMethod('get');
        $this->setAction($this->getView()->url().'#filtersexpanded');
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/filters.js', 'text/javascript'));

        // Φίλτρα
        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Φίλτρα/Αναζήτηση');
        $subform->addElement('text', 'year', array(
            'label' => 'Έτος',
            'placeholder' => 'πχ. 2012',
        ));

        $subform->addElement('text', 'employeeSearch', array(
            'label' => 'Επώνυμο απασχολούμενου',
            'placeholder' => 'πχ. Καραπετρίδης',
        ));

        // Αναζήτηση
        $subform->addElement('text', 'projectSearch', array(
            'label' => 'Έργο',
            'placeholder' => 'πχ. Ψηφιακές Υπηρεσίες ΤΕΙ Αθήνας',
        ));

        // Add the submit button
        $subform->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Εφαρμογή',
            'class' => 'addbutton',
        ));
        $this->addSubForm($subform, 'filters');
    }
}
?>