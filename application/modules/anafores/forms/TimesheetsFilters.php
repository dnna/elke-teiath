<?php
class Anafores_Form_TimesheetsFilters extends Dnna_Form_FormBase {
    public function init() {
        $this->setMethod('get');
        $this->setAction($this->getView()->url().'#filtersexpanded');
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/anafores/apasxoloumenoi/filters.js', 'text/javascript'));

        // Φίλτρα
        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Φίλτρα/Αναζήτηση');

        $subform->addElement('select', 'year', array(
            'label' => 'Έτος',
            'multiOptions'  =>  array_combine(range((int)date('Y')-20, (int)date('Y')+5), range((int)date('Y')-20, (int)date('Y')+5)),
            'value' =>  (int)date('Y'),
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