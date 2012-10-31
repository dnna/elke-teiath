<?php
class  Aitiseis_Form_OikonomikiDiaxeirisiErgou extends Aitiseis_Form_Aitisi {

    protected $_partnerFieldsCount = 20;

    protected function addFinancialDetailsFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }
        // Κατηγορία
        $projectcategorysubform = new Dnna_Form_SubFormBase();
        $projectcategorysubform->addElement('select', 'id', array(
            'required' => true,
            'label' => 'Κατηγορία:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ProjectCategory')
        ));
        $subform->addSubForm($projectcategorysubform, 'category', false);
        // Προϋπολογισμός Έργου
        $subform->addElement('text', 'totalbudget', array(
            'label' => 'Συνολικός Προϋπολογισμός Έργου:',
            'required' => true,
            'validators' => array(
            array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        // Προϋπολογισμός για το ΤΕΙ Αθήνας
        $subform->addElement('text', 'teibudget', array(
            'label' => 'Προϋπολογισμός για το ΤΕΙ Αθήνας:',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        
        if($createSubform) {
            $subform->setLegend('Κατηγορία/Συνολικός Προϋπολογισμός');
            $this->addSubForm($subform, 'default');
        }
    }

    // Συνεργαζόμενοι Φορείς
    protected function addPartnerFields(&$subform = null) {
        for($i = 1; $i <= $this->_partnerFieldsCount; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_Partner($i, $this->_view, false), $i, null, 'partners');
            $subform->getSubForm($i)->setAttrib('class', 'partner');
        }

        $subform->addElement('button', 'addPartner', array(
            'label' => 'Προσθήκη Νέου Φορέα',
            'class' => 'partnerbuttons addButton',
        ));
    }

    // Αναλυτικός Προϋπολογισμός
    protected function addBudgetItemFields(&$subform = null) {
        // Αντικείμενα 1-10
        for($i = 1; $i <= 10; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_BudgetItem(), $i, null, 'budgetitems');
            $subform->getSubForm($i)->setAttrib('class', 'tableSimRow');
        }

        $subform->addElement('text', 'sum', array(
            'label' => 'Σύνολο:',
            'readonly' => true,
            'ignore' => true
        ));
        $subform->getElement('sum')->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'tableSimRight'));
        $subform->getElement('sum')->addDecorator('AnyMarkup', array('markup' => '<div class="tableSimClear"></div>', 'placement' => 'append'));
        $subform->addElement('button', 'addBudgetItem', array(
            'label' => 'Προσθήκη Νέας Δαπάνης',
            'class' => 'budgetbuttons addButton',
        ));
    }

    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.calculation.js', 'text/javascript'));

        // Στοιχεία Αίτησης
        $this->addSubForm(new Application_Form_Subforms_AitisiSelect(array('type' => 'ypovoliergou', 'approved' => true), $this->_view), 'parentaitisi');
        $this->getSubForm('parentaitisi')->setLegend('Εγκεκριμένη Αίτηση Υποβολής Έργου');

        // Συνεργαζόμενοι Φορείς
        $partners = new Dnna_Form_SubFormBase($this->_view);
        $this->addPartnerFields($partners);
        $this->addSubForm($partners, 'partners');
        $this->getSubForm('partners')->setLegend('Συνεργαζόμενοι Φορείς');

        //$this->addSubForm(new Application_Form_Subforms_AgencySelect('Φορέας Χρηματοδότησης', null, $this->_view), 'fundingagency');

        $this->addFinancialDetailsFields();

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addBudgetItemFields($subform);
        $this->addSubForm($subform, 'budgetitems');
        $this->getSubForm('budgetitems')->setLegend('Αναλυτικός Προϋπολογισμός');

        $this->addSubmitFields();
    }
}
?>