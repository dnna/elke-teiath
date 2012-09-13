<?php
class Erga_Form_Subforms_ParentSubProjectFields extends Dnna_Form_SubFormBase {
    public function init() {
        // Project ID πατρικού έργου
        $parentprojectid = Zend_Controller_Front::getInstance()->getRequest()->getParam('parentprojectid');
        $this->addElement('hidden', 'projectid', array(
            'order' => 150,
            'value' => $parentprojectid,
            'readonly' => true,
            )
        );
        // Τίτλος έργου
        $this->addElement('text', 'subprojecttitle', array(
            'label' => 'Τίτλος (στα ελληνικά):',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Τίτλος έργου (Αγγλικά)
        $this->addElement('text', 'subprojecttitleen', array(
            'label' => 'Τίτλος (στα αγγλικά):',
            'readonly' => true,
            'ignore' => true,
            )
        );
        /*// Ημερομηνία Έναρξης
        $this->addElement('text', 'subprojectstartdate', array(
            'label' => 'Ημερομηνία έναρξης:',
            'readonly' => true,
            'ignore' => true,
        ));
        // Ημερομηνία Λήξης (ίδια γραμμή)
        $this->addElement('text', 'subprojectenddate', array(
            'label' => 'Ημερομηνία λήξης:',
            'readonly' => true,
            'ignore' => true,
        ));
        // Προϋπολογισμός Υποέργου (με ΦΠΑ) ΔΕΝ ΛΕΙΤΟΥΡΓΕΙ
        $this = new Dnna_Form_SubFormBase($this->_view);
        $this->addElement('text', 'subprojectbudgetwithfpa', array(
            'label' => 'Προϋπολογισμός Έργου (με ΦΠΑ):',
            'readonly' => true,
            'ignore' => true,
        ));*/
    }
}
?>