<?php
class Praktika_Form_Committee_Filters extends Dnna_Form_FormBase {
    public function __construct($view = null) {
        parent::__construct($view);
    }
    
    public function init() {
        $this->setMethod('get');
        $this->setAction($this->getView()->url().'#filtersexpanded');
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/praktika/epitropes/filters.js', 'text/javascript'));

        // Φίλτρα
        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Φίλτρα/Αναζήτηση');
        $epitropestypes = Praktika_Model_CommitteeBase::getEpitropesTypesText();
        $epitropestypes[""] = 'Αδιάφορο';
        ksort($epitropestypes);
        $subform->addElement('select', 'competitiontype', array(
            'label' => 'Είδος Επιτροπής',
            'multiOptions' => $epitropestypes,
        ));

        // Αναζήτηση
        $subform->addElement('text', 'search', array(
            'label' => 'Αναζήτηση Μέλους',
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