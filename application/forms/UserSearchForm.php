<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_UserSearchForm extends Dnna_Form_FormBase {
    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/users/usersearch.js', 'text/javascript'));

        $this->addSubForm(new Application_Form_Subforms_SupervisorSelect(null, $this->_view), 'supervisor');
        $this->getSubForm('supervisor')->setLegend('Στοιχεία Επιστημονικά Υπεύθυνου');
        $this->addExpandImg('supervisor');
    }
}
?>