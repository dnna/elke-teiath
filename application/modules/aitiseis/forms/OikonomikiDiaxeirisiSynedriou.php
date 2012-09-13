<?php
class  Aitiseis_Form_OikonomikiDiaxeirisiSynedriou extends Aitiseis_Form_Aitisi {
    protected function addFinancialDetailsFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }
        // Προϋπολογισμός Έργου
        $subform->addElement('text', 'title', array(
            'label' => 'Τίτλος:',
            'required' => true,
        ));
        // Προϋπολογισμός Έργου
        $subform->addElement('text', 'totalbudget', array(
            'label' => 'Προϋπολογισμός:',
            'required' => true,
            'validators' => array(
            array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        
        if($createSubform) {
            $subform->setLegend('Στοιχεία Συνεδρίου');
            $this->addSubForm($subform, 'default');
        }
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

        $this->addFinancialDetailsFields();

        $this->addSubForm(new Application_Form_Subforms_AgencySelect('Φορέας Χρηματοδότησης'), 'fundingagency');

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addBudgetItemFields($subform);
        $this->addSubForm($subform, 'budgetitems');
        $this->getSubForm('budgetitems')->setLegend('Αναλυτικός Προϋπολογισμός');

        $this->addSubmitFields();
    }
}
?>