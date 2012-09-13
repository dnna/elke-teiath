<?php
class Erga_Form_ErgaFilters extends Dnna_Form_FormBase {
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
        $subform->addElement('select', 'showcompletes', array(
            'label' => 'Ολοκληρωμένα',
            'multiOptions' => Array(null => 'Αδιάφορο', 'true' => 'Ναί', 'false' => 'Όχι'),
            'value' => 'false'
        ));

        $subform->addElement('select', 'showoverdues', array(
            'label' => 'Έχουν εκπρόθεσμα παραδοτέα',
            'multiOptions' => Array(null => 'Αδιάφορο', 'true' => 'Ναί', 'false' => 'Όχι'),
            'value' => null
        ));

        // Αναζήτηση
        $subform->addElement('text', 'search', array(
            'label' => 'Αναζήτηση',
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