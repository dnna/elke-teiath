<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Aitiseis_Form_DhmiourgiaEpitropisParalavis extends Aitiseis_Form_Aitisi {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));

        // Επιλογή έργου
        $this->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => true), $this->_view), 'project');

        /*$subform = new Dnna_Form_SubFormBase();
        Praktika_Form_Committee_Member::addCommitteeMembers($subform, 'receiptcommittee');
        $this->addSubForm($subform, 'receiptcommittee', false);*/

        $this->addSubmitFields();
    }
}

?>