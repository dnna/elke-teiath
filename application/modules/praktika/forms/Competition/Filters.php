<?php
class Praktika_Form_Competition_Filters extends Dnna_Form_FormBase {
    public function __construct($view = null) {
        parent::__construct($view);
    }
    
    public function init() {
        $this->setMethod('get');
        $this->setAction($this->getView()->url().'#filtersexpanded');
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/praktika/diagonismoi/filters.js', 'text/javascript'));

        // Φίλτρα
        $subform = new Dnna_Form_SubFormBase();
        $subform->setLegend('Φίλτρα/Αναζήτηση');
        $competitiontypes = Praktika_Model_Competition::getCompetitionTypes();
        $competitiontypes[""] = 'Αδιάφορο';
        ksort($competitiontypes);
        $subform->addElement('select', 'competitiontype', array(
            'label' => 'Τύπος Διαγωνισμού',
            'multiOptions' => $competitiontypes,
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