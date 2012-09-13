<?php
class  Aitiseis_Form_Daneismou extends Aitiseis_Form_Aitisi {
    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.calculation.js', 'text/javascript'));

        // Aitisiid
        $this->addElement('hidden', 'aitisiid', array());
        // Επιλογή έργου
        $this->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => true), $this->_view), 'project');

        $subform = new Aitiseis_Form_Subforms_LoanItems_LoanItems(null, $this->_view);
        $this->addSubForm($subform, 'loanitems');
        $this->getSubForm('loanitems')->setLegend('Δαπάνες');

        $this->addSubmitFields();
    }
}
?>