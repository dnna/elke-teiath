<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Aitiseis_Form_OnomastikiKatastasi extends Aitiseis_Form_Aitisi {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headLink()->appendStylesheet($this->_view->baseUrl('media/css/jquery.autocomplete.css'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.autocomplete.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));

        // Στοιχεία Αίτησης
        $this->addSubForm(new Application_Form_Subforms_AitisiSelect(array('type' => 'ypovoliergou', 'approved' => true), $this->_view), 'parentaitisi');
        $this->getSubForm('parentaitisi')->setLegend('Εγκεκριμένη Αίτηση Υποβολής Έργου');

        $subform = new Dnna_Form_SubFormBase($this->_view);
        for($i = 1; $i <= 20; $i++) {
            $subform->addSubForm(new Aitiseis_Form_Subforms_Employee($i, $this->_view, true), $i, null, 'employees');
            $subform->getSubForm($i)->setLegend('Απασχολούμενος '.$i);
            //$subform->addExpandImg($i, false, 'toggleEmployeeDetails_'.$i);
        }

        $subform->addElement('button', 'addEmployee', array(
            'label' => 'Προσθήκη Απασχολούμενου',
            'class' => 'employeebuttons addButton',
        ));
        $this->addSubForm($subform, 'employees', false);

        $this->addSubmitFields();
    }
}

?>