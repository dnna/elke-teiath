<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Aitiseis_Form_OrismosEpitropisDiagonismou extends Aitiseis_Form_Aitisi {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));

        // Επιλογή έργου
        $this->addSubForm(new Application_Form_Subforms_SubProjectSelect(array('required' => true), $this->_view), 'subproject');

        $subform = new Praktika_Form_Competition($this->_view);
        $subform->removeSubForm('subproject');
        $subform->initSubform();
        $this->addSubForm($subform, 'competition', false);

        /*$subform = new Dnna_Form_SubFormBase();
        Praktika_Form_Committee_Member::addCommitteeMembers($subform, 'competitioncommittee');
        $subform->setLegend('Προτεινόμενα μέλη Επιτροπής Διενέργειας Διαγωνισμού');
        $this->addSubForm($subform, 'competitioncommittee');

        $subform = new Dnna_Form_SubFormBase();
        Praktika_Form_Committee_Member::addCommitteeMembers($subform, 'objectioncommittee');
        $subform->setLegend('Προτεινόμενα μέλη Επιτροπής Ενστάσεων');
        $this->addSubForm($subform, 'objectioncommittee');*/

        $this->addSubmitFields();
    }
}

?>