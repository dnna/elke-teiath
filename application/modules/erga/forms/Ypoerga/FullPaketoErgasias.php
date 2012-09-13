<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Ypoerga_FullPaketoErgasias extends Erga_Form_Ypoerga_PaketoErgasias {
    public function __construct($workpackage, $view = null) {
        parent::__construct($view, $workpackage);
    }

    public function init() {
        parent::init();
        $deliverables = new Dnna_Form_SubFormBase();
        $i = 1;
        $this->_view->workpackage = $this->_workpackage;
        foreach($this->_workpackage->get_deliverables() as $curDeliverable) {
            $deliverable = new Erga_Form_Ypoerga_FullParadoteo($curDeliverable, $this->_view);
            $deliverable->set_empty(false);
            $deliverables->addSubForm($deliverable, $i++);
        }
        unset($this->_view->workpackage);
        $this->addSubForm($deliverables, 'deliverables');
    }
}

?>