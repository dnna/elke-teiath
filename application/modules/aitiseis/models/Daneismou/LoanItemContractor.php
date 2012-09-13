<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity
 */
class Aitiseis_Model_Daneismou_LoanItemContractor extends Aitiseis_Model_Daneismou_LoanItem {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectContractor")
     * @JoinColumn (name="contractor", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_SubProjectContractor
     */
    protected $_contractor;

    public function get_contractor() {
        return $this->_contractor;
    }

    public function set_contractor($_contractor) {
        $this->_contractor = $_contractor;
    }

    public function getAttachedObject() {
        return $this->get_contractor();
    }

    public function __toString() {
        return $this->get_contractor()->get_agency()->get_name();
    }
}
?>