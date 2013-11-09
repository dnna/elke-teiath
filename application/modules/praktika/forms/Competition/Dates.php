<?php
class Praktika_Form_Competition_Dates extends Dnna_Form_SubFormBase {
    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        // Είδος Διαγωνισμού
        $this->addElement('select', 'competitiontype', array(
            'required' => true,
            'label' => 'Είδος Διαγωνισμού:',
            'multiOptions' => Praktika_Model_Competition::getCompetitionTypes(),
        ));
        // Είδος Προμήθειας
        $this->addElement('select', 'procurementtype', array(
            'required' => true,
            'label' => 'Είδος Προμήθειας:',
            'multiOptions' => Praktika_Model_Competition::getProcurementTypes(),
        ));
        // Αρ. Πρωτ. Ανάθεσης
        $this->addElement('text', 'refnumassignment', array(
            'label' => 'Αρ. Πρωτ. Ανάθεσης:',
        ));
        // Ημ/νία Ανάθεσης
        $this->addElement('text', 'assignmentdate', array(
            'label' => 'Ημ/νία Ανάθεσης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Αρ. Πρωτ. Προκήρυξης
        $this->addElement('text', 'refnumnotice', array(
            'label' => 'Αρ. Πρωτ. Προκήρυξης:',
        ));
        // Ημ/νία Προκήρυξης
        $this->addElement('text', 'noticedate', array(
            'label' => 'Ημ/νία Προκήρυξης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Ημ/νία Διενέργειας
        $this->addElement('text', 'execdate', array(
            'label' => 'Ημ/νία Διενέργειας:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Αποτέλεσμα Διαγωνισμού
        $this->addElement('select', 'result', array(
            'label' => 'Αποτέλεσμα Διαγωνισμού:',
            'multiOptions' => Praktika_Model_Competition::getResults(),
        ));
        // Απόφαση Έγκρισης Αποτελέσματος
        $this->addElement('text', 'refnumresultapproved', array(
            'label' => 'Απόφαση Έγκρισης Αποτελέσματος:',
        ));
        // Ημ/νία Απόφασης Έγκρισης Αποτελέσματος
        $this->addElement('text', 'resultapproveddate', array(
            'label' => 'Ημ/νία Απόφ. Έγκρισης Αποτελ.:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Απόφαση Κατακύρωσης
        $this->addElement('text', 'refnumaward', array(
            'label' => 'Απόφαση Κατακύρωσης:',
        ));
        // Ημ/νία Κατακύρωσης
        $this->addElement('text', 'awarddate', array(
            'label' => 'Ημ/νία Κατακύρωσης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
    }
}
?>