<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Apasxoloumenoi_Employee extends Erga_Form_Ypoerga_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headLink()->appendStylesheet($this->_view->baseUrl('media/css/jquery.autocomplete.css'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.autocomplete.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/apasxoloumenoi/apasxoloumenos.js', 'text/javascript'));

        // Απόφαση Έγκρισης ΕΕΕ
        $subform = new Aitiseis_Form_Subforms_Employee('1', $this->_view);
        $subform->addElement('text', 'refnumapproved', array(
            'label' => 'Απόφαση Έγκρισης ΕΕΕ',
            'order' => 1,
            )
        );
        $subform->addElement('text', 'contractnum', array(
            'label' => 'Αριθμός Σύμβασης',
            'order' => 2,
            )
        );

        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>