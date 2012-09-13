<?php
class  Erga_Form_PersonnelCategories extends Dnna_Form_FormBase {
    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $defaultform = new Dnna_Form_SubFormBase();
        for($i = 1; $i <= 10; $i++) {
            // Φόρμα Κατηγορίες Προσωπικού
            $subform = new Dnna_Form_SubFormBase($this->_view);
            // Recordid
            $subform->addElement('hidden', 'recordid', array());
            // Name
            $subform->addElement('text', 'name', array(
                'required' => true,
                'label' =>  'Όνομα Κατηγορίας',
                'placeholder'   =>  'π.χ. Επιστημονικό Προσωπικό',
            ));
            $defaultform->addSubForm($subform, $i, true, 'personnelcategories');
        }
        
        $defaultform->setLegend('Κατηγορίες Προσωπικού');
        $defaultform->addElement('button', 'addPersonnelCategory', array(
            'label' => 'Προσθήκη Κατηγορίας Προσωπικού',
            'class' => 'personnelcategoriesbuttons addButton',
        ));
        $this->addSubForm($defaultform, 'personnelcategories', true);
        $defaultform->addSubmitFields();
    }
}
?>