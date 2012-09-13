<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_EntoliPliromis extends Aitiseis_Form_Aitisi {
    protected $_deliverablesCount = 20;

    protected function addDeliverableFields(&$subform) {
        $deliverablessbuform = new Dnna_Form_SubFormBase($this->_view);
        $deliverablessbuform->setLegend('Επιλογή Παραδοτέων');
        for($i = 1; $i <= $this->_deliverablesCount; $i++) {
            $deliverablessbuform->addSubForm(new Aitiseis_Form_Subforms_Deliverable($this->_view), $i, null, 'deliverables');
        }
        $deliverablessbuform->addElement('button', 'addDeliverable', array(
            'label' => 'Προσθήκη Παραδοτέου',
            'class' => 'deliverablebuttons addButton',
        ));
        $this->addSubForm($deliverablessbuform, 'deliverables');
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));

        $subform = new Dnna_Form_SubFormBase($this->_view);
        // Επιλογή Έργου/Υποέργου
        $subform->addSubForm(new Application_Form_Subforms_SubProjectSelect(array('required' => true), $this->_view), 'subproject', false);
        // Επιλογή Δικαιούχου
        $subsubform = new Dnna_Form_SubFormBase();
        if($this->_aitisi != null && $this->_aitisi->get_recipientauthor() != null) {
            $employee = array($this->_aitisi->get_recipientauthor()->get_recordid() => $this->_aitisi->get_recipientauthor()->__toString());
        } else {
            $employee = array();
        }
        if($this->_aitisi != null && $this->_aitisi->get_recipientcontractor() != null) {
            $contractor = array($this->_aitisi->get_recipientcontractor()->get_recordid() => $this->_aitisi->get_recipientcontractor()->__toString());
        } else {
            $contractor = array();
        }
        $subsubform->addElement('select', 'recordid', array(
            'label' => 'Δικαιούχος (Σύμβαση):',
            'registerInArrayValidator' => false,
            'multiOptions' => $employee,
        ));
        $subform->addSubForm($subsubform, 'recipientauthor', false);
        $subsubform = new Dnna_Form_SubFormBase();
        $subsubform->addElement('select', 'recordid', array(
            'label' => 'Δικαιούχος (Σύμβαση):',
            'registerInArrayValidator' => false,
            'multiOptions' => $contractor,
        ));
        $subform->addSubForm($subsubform, 'recipientcontractor', false);
        // Ποσό Εντολής (€):
        $subform->addElement('text', 'amount', array(
            'label' => 'Ποσό Εντολής (€):',
            'required' => true,
            'validators' => array(
            array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        // Είδος Πληρωμής
        $subform->addElement('select', 'type', array(
            'required' => true,
            'label' => 'Είδος Πληρωμής:',
            'multiOptions' => Aitiseis_Model_EntoliPliromis::getConstantAsArray('TYPE')
        ));
        // Είδος Παραστατικού (αν το είδος πληρωμής είναι Αμοιβή)
        $subform->addElement('select', 'vouchertype', array(
            'required' => true,
            'label' => 'Είδος Παραστατικού:',
            'multiOptions' => Aitiseis_Model_EntoliPliromis::getConstantAsArray('VOUCHERTYPE')
        ));
        // Υποτύπος (αν το είδος ΔΕΝ είναι Αμοιβή)
        $subform->addElement('select', 'subtype', array(
            'label' => 'Τύπος:',
            'multiOptions' => Aitiseis_Model_EntoliPliromis::getConstantAsArray('SUBTYPE')
        ));
        // Αιτιολογία
        $subform->addElement('textarea', 'reasoning', array(
            'label' => 'Αιτιολογία:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => $this->_textareaRows,
            'cols' => $this->_textareaCols,
            'required' => true,
        ));
        // Κωδικός Λογιστικής
        $subform->addElement('text', 'acccode', array(
            'label' => 'Κωδικός Λογιστικής:',
            )
        );
        // Κατηγορία δαπάνης (από την σχετική λίστα)
        $subform->addElement('select', 'expenditurecategory', array(
            'label' => 'Κατηγορία Δαπάνης:',
            'required' => true,
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ExpenditureCategory')
        ));
        // Τρόπος Πληρωμής
        $subform->addElement('select', 'paymentmethod', array(
            'required' => true,
            'label' => 'Τρόπος Πληρωμής:',
            'multiOptions' => Aitiseis_Model_EntoliPliromis::getConstantAsArray('PAYMENTMETHOD')
        ));
        // Τραπεζικός λογαριασμός δικαιούχου
        $subform->addElement('text', 'recbankaccount', array(
            'label' => 'Τραπεζικός λογαριασμός δικαιούχου:',
        ));

        $this->addSubForm($subform, 'default');
        // Επιλογή παραδοτέων
        $this->addDeliverableFields($subform);

        $this->addSubmitFields();
    }
}
?>