<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Form_ChangeApproval extends Dnna_Form_FormBase {
    protected function addApprovedFields(&$subform = Array(), $createNewSubform = false) {
        $subform->addElement('hidden', 'id', array());
        // Τίτλος Έργου
        $subform->addElement('text', 'projecttitle', array(
            'label' => 'Τίτλος Έργου:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Όνομα απασχολούμενου
        $subform->addElement('text', 'employeename', array(
            'label' => 'Ονοματεπώνυμο Απασχολούμενου:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Μήνας/Έτος
        $subform->addElement('text', 'monthyear', array(
            'label' => 'Μήνας/Έτος:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Συνολικές Ώρες Πριν
        $subform->addElement('text', 'hoursbefore', array(
            'label' => 'Συνολικές Ώρες Πριν:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Συνολικές Ώρες Μετα
        $subform->addElement('text', 'hoursafter', array(
            'label' => 'Συνολικές Ώρες Μετά:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Μέγιστες Επιτρεπτές Ώρες
        $subform->addElement('text', 'hoursallowed', array(
            'label' => 'Μέγιστες Επιτρεπτές Ώρες:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Έχει εγκριθεί;
        $subform->addElement('select', 'approved', array(
            'label' => 'Κατάσταση Έγκρισης',
            'required' => true,
            'multiOptions' => Array(
                Aitiseis_Model_AitisiBase::APPROVED => 'Εγκρίθηκε',
                Aitiseis_Model_AitisiBase::REJECTED => 'Απορρίφθηκε',
                /*Aitiseis_Model_AitisiBase::PENDING => 'Δεν έχει αποφασιστεί'*/
                ),
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
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>