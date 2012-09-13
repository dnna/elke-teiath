<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Praktika_Form_Committee_Paralavis extends Dnna_Form_SubFormBase {
    public function init() {
        $this->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => false), $this->_view), 'project', false);
        Praktika_Form_Committee::addGenCommitteeFields($this);
    }
}
?>