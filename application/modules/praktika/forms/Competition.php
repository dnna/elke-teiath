<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Praktika_Form_Competition extends Dnna_Form_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));

        // Επιλογή έργου
        $this->addSubForm(new Application_Form_Subforms_SubProjectSelect(array('required' => true), $this->_view), 'subproject');

        $technicalconsultant = new Dnna_Form_AutoForm('Application_Model_Consultant', $this->_view);
        $technicalconsultant->initSubform();
        $technicalconsultant->setLegend('Ονοματεπώνυμο και Ιδιότητα Συμβούλου για Τεχνικά Θέματα');
        $this->addSubForm($technicalconsultant, 'technicalconsultant');

        $responsibleperson = new Dnna_Form_AutoForm('Application_Model_Consultant', $this->_view);
        $responsibleperson->initSubform();
        $responsibleperson->setLegend('Υπεύθυνος διαγωνισμού για πληροφορίες/προδιαγραφές');
        $this->addSubForm($responsibleperson, 'responsibleperson');

        $subform = new Dnna_Form_AutoForm('Praktika_Model_Competition', $this->_view);
        $subform->initSubform();
        $subform->removeElement('submit');
        $datessubform = new Praktika_Form_Competition_Dates(null, $this->_view);
        foreach($datessubform->getElements() as $curElement) {
            $subform->addElement($curElement, null, array('order' => 0));
        }
        $subform->setAttrib('class', 'biglabels');
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>