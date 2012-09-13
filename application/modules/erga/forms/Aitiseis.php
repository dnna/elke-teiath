<?php
class  Erga_Form_Aitiseis extends Dnna_Form_SubFormBase {
    protected $_project;

    public function __construct($view = null, $project = null) {
        $this->_project = $project;
        parent::__construct($view);
    }

    public function init() {
        $subform = new Dnna_Form_SubFormBase($this->_view);
        for($i = 1; $i <= 20; $i++) {
            // Αίτηση
            //if($this->_project->get_iscomplex() == 0) {
                $subform->addSubForm(new Application_Form_Subforms_AitisiSelect(array(/*'approved' => 1,*/'required' => false, 'selecttype' => true), $this->_view), $i, true, 'default-aitiseis');
                $subform->getSubForm($i)->setLegend('Αίτηση '.$i);
            /*} else {
                $subform->addSubForm(new Dnna_Form_SubFormBase($this->_view), 'aitisiypovolisergoupropform');
                $subform->getSubForm('aitisiypovolisergoupropform')->setLegend('Αίτηση Έγκρισης Υποβολής Έργου');
                $subform->getSubForm('aitisiypovolisergoupropform')->addElement('text', 'aitisiypovolisergouprop', array(
                    'label' => 'Επιλογή Αίτησης:',
                    'value' => 'Αδύνατη - Επιτρέπονται Υποέργα',
                    'ignore' => true,
                    'disabled' => true,
                    'readonly' => true,
                ));
            }*/
        }
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/projectaitiseis.js', 'text/javascript'));
        $subform->addElement('button', 'addAitisi', array(
            'label' => 'Προσθήκη Αίτησης',
            'class' => 'aitisibuttons addButton',
        ));
        $this->addSubForm($subform, 'aitiseis', false);
    }
}
?>