<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.budgetitems")
 */
class Erga_Model_SubItems_BudgetItem extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectFinancialDetails", inversedBy="_budgetitems")
     * @JoinColumn (name="financialdetailsid", referencedColumnName="financialdetailsid")
     */
    protected $_financialdetails;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_ExpenditureCategory")
     * @JoinColumn (name="expenditurecategoryid", referencedColumnName="id")
     * @var Application_Model_Lists_ExpenditureCategory
     */
    protected $_category;
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount;

    public function get_financialdetails() {
        return $this->_financialdetails;
    }

    public function set_financialdetails($_financialdetails) {
        $this->_financialdetails = $_financialdetails;
    }

    public function get_category() {
        return $this->_category;
    }

    public function set_category($_category) {
        $this->_category = $_category;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }
    
    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectFinancialDetails) {
            $this->set_financialdetails($owner);
        }
    }

    public function __toString() {
        return $this->get_category()->__toString();
    }
}
?>
