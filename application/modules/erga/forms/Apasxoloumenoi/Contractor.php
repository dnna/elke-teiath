<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Apasxoloumenoi_Contractor extends Erga_Form_Ypoerga_FormBase {
    protected function addContractorFields(&$subform = null) {
        // Recordid
        $subform->addElement('hidden', 'recordid', array());
        $subform->addSubForm(new Application_Form_Lists_SubFormEdit('Application_Model_Lists_Agency'), 'agency', false);
        // Υπεύθυνος Επικοινωνίας
        $subform->addElement('text', 'contact', array(
            'label' => 'Υπεύθυνος Επικοινωνίας:',
            'required' => true,
        ));
        // Τηλέφωνο
        $subform->addElement('text', 'phone', array(
            'label' => 'Τηλέφωνο:',
            'required' => true,
        ));
        // Email
        $subform->addElement('text', 'email', array(
            'label' => 'Email:',
            'required' => true,
        ));
        // Απόφαση Οριστικής Κατακύρωσης
        $subform->addElement('text', 'refnumapproved', array(
                'label' => 'Απόφαση Οριστικής Κατακύρωσης',
            )
        );
        // Αριθμός Σύμβασης
        $subform->addElement('text', 'contractnum', array(
                'label' => 'Αριθμός Σύμβασης',
            )
        );
        // Αρ. Πρωτ. Σύμβασης
        $subform->addElement('text', 'refnumcontract', array(
            'label' => 'Αρ. Πρωτ. Σύμβασης:',
            'required' => true,
        ));
        // Ποσό Σύμβασης (με ΦΠΑ)
        $subform->addElement('text', 'amount', array(
            'label' => 'Ποσό Σύμβασης (με ΦΠΑ):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
            'required' => true,
        ));
        // Διάρκεια Σύμβασης (ΑΠΟ – ΕΩΣ)
        $subform->addElement('text', 'startdate', array(
            'label' => 'Ημ/νία έναρξης σύμβασης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        $subform->addElement('text', 'enddate', array(
            'label' => 'Ημ/νία λήξης σύμβασης:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));
        // Ημ/νία Προσωρινής Παραλαβής
        $subform->addElement('text', 'provisionalacceptancedate', array(
            'label' => 'Ημ/νία Προσωρινής Παραλαβής:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Ημ/νία Οριστικής Παραλαβής
        $subform->addElement('text', 'finalacceptancedate', array(
            'label' => 'Ημ/νία Οριστικής Παραλαβής:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Ημ/νία Αποπληρωμής
        $subform->addElement('text', 'repaymentdate', array(
            'label' => 'Ημ/νία Αποπληρωμής:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
        ));
        // Σχόλια
        $subform->addElement('textarea', 'comments', array(
            'label' => 'Γενικές Πληροφορίες:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
        ));
    }
    
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headLink()->appendStylesheet($this->_view->baseUrl('media/css/jquery.autocomplete.css'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.autocomplete.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/apasxoloumenoi/anadoxos.js', 'text/javascript'));

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addContractorFields($subform);
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>