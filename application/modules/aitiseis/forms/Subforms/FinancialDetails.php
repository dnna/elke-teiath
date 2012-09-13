<?php
class Aitiseis_Form_Subforms_FinancialDetails extends Dnna_Form_SubFormBase {

    public function init() {
        // Οικονομικά στοιχεία έργου
        // Αναλυτικός Προϋπολογισμός
        $subform = new Dnna_Form_SubFormBase();
        // Αντικείμενο 1 (Απαραίτητο)
        $subform->addSubForm(new Aitiseis_Form_Subforms_BudgetItem(true), 1, null, 'default-budgetitems');
        $subform->getSubForm(1)->getElement('isvisible')->setValue('1'); // Αρχίζει visible

        // Αντικείμενα 2-10
        for($i = 2; $i <= 10; $i++) {
            $subform->addSubForm(new Aitiseis_Form_Subforms_BudgetItem(), $i, null, 'default-budgetitems');
        }

        $subform->addElement('button', 'addBudgetItem', array(
            'label' => 'Προσθήκη Νέας Κατηγορίας',
            'class' => 'budgetbuttons addButton',
        ));
        $this->addSubForm($subform, 'budgetitems');
        // Τέλος Αναλυτικού Προϋπολογισμού
        // Κατηγορία
        $projectcategorysubform = new Dnna_Form_SubFormBase();
        $projectcategorysubform->addElement('select', 'id', array(
            'required' => true,
            'label' => 'Κατηγορία:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ProjectCategory')
        ));
        $this->addSubForm($projectcategorysubform, 'category', false);
        // Σκοπός του έργου
        $this->addElement('textarea', 'goal', array(
            'label' => 'Σκοπός του έργου:',
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
        // Ποσοστό παρακράτησης υπέρ ΕΛΚΕ
        $this->addElement('text', 'elkededuction', array(
            'label' => 'Ποσοστό παρακράτησης υπέρ ΕΛΚΕ (συμπληρώνεται από την υπηρεσία):',
            'readonly' => true,
        ));
    }
}
?>
