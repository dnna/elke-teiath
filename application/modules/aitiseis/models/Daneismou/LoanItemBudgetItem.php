<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity
 */
class Aitiseis_Model_Daneismou_LoanItemBudgetItem extends Aitiseis_Model_Daneismou_LoanItem {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_BudgetItem")
     * @JoinColumn (name="budgetitem", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_BudgetItem
     */
    protected $_budgetitem;

    protected $_category;

    public function get_budgetitem() {
        return $this->_budgetitem;
    }

    public function set_budgetitem($_budgetitem) {
        $this->_budgetitem = $_budgetitem;
    }

    public function get_category() {
        return $this->get_budgetitem()->get_category();
    }

    public function getAttachedObject() {
        return $this->get_budgetitem();
    }

    public function __toString() {
        return $this->get_budgetitem()->get_category()->get_name();
    }
}
?>