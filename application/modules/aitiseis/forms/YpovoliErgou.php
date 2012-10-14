<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_YpovoliErgou extends Aitiseis_Form_Aitisi {
    protected function addStoixeiaErgouFields(&$subform) {
        // Κωδικός Αίτησης
        $subform->addElement('hidden', 'aitisiid', array(
            'label' => 'Κωδικός Έργου:',
            )
        );
        // Τίτλος
        $subform->addElement('textarea', 'title', array(
            'label' => 'Τίτλος 1:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );
        // Τίτλος (Αγγλικά)
        $subform->addElement('textarea', 'titleen', array(
            'label' => 'Τίτλος 2:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            )
        );
        // Περιγραφή
        $subform->addElement('textarea', 'description', array(
            'label' => 'Περιγραφή:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
            'required' => true,
        ));
        // Ημερομηνία Έναρξης
        $subform->addElement('text', 'startdate', array(
            'label' => 'Ημερομηνία έναρξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Ημερομηνία Λήξης (ίδια γραμμή)
        $subform->addElement('text', 'enddate', array(
            'label' => 'Ημερομηνία λήξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Φορέας Χρηματοδότησης
        $subform->addSubForm(new Application_Form_Subforms_AgencySelect('Φορέας Χρηματοδότησης'), 'fundingagency', false);
        // Φορέας Συγχρηματοδότησης
        $subform->addSubForm(new Application_Form_Subforms_AgencySelect('Φορέας Συγχρηματοδότησης', false), 'cofundingagency', false);
        // Ανάδοχος Φορέας Έργου
        $subform->addSubForm(new Application_Form_Subforms_AgencySelect('Ανάδοχος Φορέας Έργου', false), 'contractor', false);
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
        // Εθνική Συμμετοχή
        $subform->addElement('text', 'nationalparticipation', array(
            'label' => 'Εθνική Συμμετοχή (%):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            //'class' => 'formatFloat',
        ));
        // Κοινοτική Συμμετοχή
        $subform->addElement('text', 'europeanparticipation', array(
            'label' => 'Κοινοτική Συμμετοχή (%):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            //'class' => 'formatFloat',
        ));
        // Άλλες παρατηρήσεις
        $subform->addElement('textarea', 'comments', array(
            'label' => 'Άλλες παρατηρήσεις:',
            /*'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),*/
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));

        // Επιστημονικά Υπεύθυνος
        $this->addSubForm(new Application_Form_Subforms_SupervisorView($this->_aitisi->get_supervisor(), $this->_view), 'supervisor');
        $this->getSubForm('supervisor')->setLegend('Στοιχεία Επιστημονικά Υπεύθυνου');
        $this->addExpandImg('supervisor');

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addStoixeiaErgouFields($subform);
        $subform->setLegend('Στοιχεία Έργου');
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>