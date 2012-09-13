<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity
 */
class Aitiseis_Model_Daneismou_LoanItemEmployee extends Aitiseis_Model_Daneismou_LoanItem {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectEmployee")
     * @JoinColumn (name="employee", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_SubProjectEmployee
     */
    protected $_employee;

    public function get_employee() {
        return $this->_employee;
    }

    public function set_employee($_employee) {
        $this->_employee = $_employee;
    }

    public function getAttachedObject() {
        return $this->get_employee();
    }

    public function __toString() {
        return $this->get_employee()->get_employee()->get_name();
    }
}
?>