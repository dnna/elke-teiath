<?php
class Anafores_Form_EpistimonikaYpeuthinoiFilters extends Anafores_Form_ApasxoloumenoiFilters {
    public function init() {
        $this->setMethod('get');
        $this->setAction($this->getView()->url().'#filtersexpanded');
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/anafores/epistimonikaypeuthinoi/filters.js', 'text/javascript'));

        // Φίλτρα
        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Φίλτρα/Αναζήτηση');

        $subform->addElement('select', 'currentprojects', array(
            'label' => 'Μόνο τρέχοντα έργα',
            'multiOptions' => Array('true' => 'Ναί', 'false' => 'Όχι'),
            'value' => 'true'
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