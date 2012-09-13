<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_ChangeApproval extends Aitiseis_Form_Aitisi {
    protected function addApprovedFields(&$subform = Array(), $createNewSubform = false) {
        $subform->addElement('hidden', 'aitisiid', array());
        // Τίτλος
        $subform->addElement('text', 'title', array(
            'label' => 'Τίτλος Αίτησης:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Έχει εγκριθεί το σαν σύνολο;
        $subform->addElement('select', 'approved', array(
            'label' => 'Κατάσταση Έγκρισης',
            'required' => true,
            'multiOptions' => Array(
                Aitiseis_Model_AitisiBase::APPROVED => 'Εγκρίθηκε',
                Aitiseis_Model_AitisiBase::REJECTED => 'Απορρίφθηκε',
                /*Aitiseis_Model_AitisiBase::PENDING => 'Δεν έχει αποφασιστεί'*/
                ),
        ));
        // Αρ. Συνεδρίασης
        $subform->addSubForm(new Application_Form_Subforms_SynedriasiSelect(array('required' => true), $this->_view), 'session', false);
        // Αρ. Θέματος Συνεδρίασης
        $sessionsubjectsubform = new Dnna_Form_SubFormBase($this->_view);
        $sessionsubjectsubform->addElement('text', 'num', array(
            'label' => 'Αρ. Θέματος Συνεδρίασης:',
            'required' => true,
            'validators' => array(
                array('validator' => 'Digits')
            ),
        ));
        $subform->addSubForm($sessionsubjectsubform, 'sessionsubject', false);
        //$subform->addSubForm(new Application_Form_Subforms_SubjectSelect(array('required' => true), $this->_view), 'sessionsubject', false);
        // Ημερομηνία Υποβολής
        $subform->addElement('text', 'creationdate', array(
            'label' => 'Ημερομηνία Υποβολής Αίτησης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        if($createNewSubform) {
            $this->addSubForm($subform, 'default');
        }
    }
    
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addApprovedFields($subform);
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/aitiseis/changeapproval.js', 'text/javascript'));
        if(get_class($this->_aitisi) === 'Aitiseis_Model_OikonomikiDiaxeirisi') {
            $subform->addElement('text', 'elkededuction', array(
                'label' => 'Ποσοστό παρακράτησης υπέρ ΕΛΚΕ (%):',
            ));
        }
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>