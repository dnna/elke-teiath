<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Subforms_Author extends Dnna_Form_SubFormBase {
    protected $_i;
    
    public function __construct($i, $view = null) {
        $this->_i = $i;
        parent::__construct($view);
    }

    public function init() {
        $subproject = $this->_view->getSubProject();
        $project = $this->_view->getProject();
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Όνομα Συντάκτη
        $subform = new Dnna_Form_SubFormBase($this->_view);
        $subform->addElement('select', 'recordid', array(
            'label' => 'Επιλογή Σύμβασης:',
            'multiOptions' => $subproject->get_employeesAs2dArray()+$project->get_employeesAs2dArray()
        ));
        $this->addSubForm($subform, 'employee', false);
        // Κατηγορία Προσωπικού (αν υπάρχει)
        if($project->get_personnelcategories()->count() > 0) {
            $subform = new Dnna_Form_SubFormBase($this->_view);
            $subform->addElement('select', 'recordid', array(
                'label' => 'Κατηγορία Προσωπικού:',
                'multiOptions' => $project->get_personnelcategoriesAs2dArray()
            ));
            $this->addSubForm($subform, 'personnelcategory', false);
        }
        // Ωρομίσθιο
        $this->addElement('text', 'rate', array(
            'label' => 'Ωρομίσθιο:',
            'multiOptions' => $subproject->get_employeesAs2dArray()+$project->get_employeesAs2dArray()
        ));
        // Ποσό
        $this->addElement('text', 'amount', array(
            'label' => 'Ποσό:',
            'class' => 'formatFloat',
        ));
    }
}

?>