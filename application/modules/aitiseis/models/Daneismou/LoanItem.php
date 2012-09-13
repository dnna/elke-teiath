<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_aitiseis.dan_budgetitems")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="itemtype", type="string")
 * @DiscriminatorMap({"budgetitem" = "Aitiseis_Model_Daneismou_LoanItemBudgetItem", "employee" = "Aitiseis_Model_Daneismou_LoanItemEmployee", "contractor" = "Aitiseis_Model_Daneismou_LoanItemContractor"})
 */
abstract class Aitiseis_Model_Daneismou_LoanItem extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_Daneismou", inversedBy="_budgetitems")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     */
    protected $_aitisidaneismou;
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount;

    protected $_name;

    public function get_aitisidaneismou() {
        return $this->_aitisidaneismou;
    }

    public function set_aitisidaneismou($_aitisidaneismou) {
        $this->_aitisidaneismou = $_aitisidaneismou;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }

    public function get_name() {
        $this->_name = $this->__toString();
        return $this->_name;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Aitiseis_Model_Daneismou) {
            $this->set_aitisidaneismou($owner);
        }
    }

    public abstract function getAttachedObject();

    public abstract function __toString();
}
?>