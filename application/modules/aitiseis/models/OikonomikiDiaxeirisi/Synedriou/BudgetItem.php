<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_aitiseis.aods_budgetitems")
 */
class Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou_BudgetItem extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou", inversedBy="_budgetitems")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     */
    protected $_aitisioikonomikisdiaxeirisis;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_ExpenditureCategory")
     * @JoinColumn (name="expenditurecategoryid", referencedColumnName="id")
     * @var Application_Model_Lists_ExpenditureCategory
     */
    protected $_category;
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount;

    public function get_aitisioikonomikisdiaxeirisis() {
        return $this->_aitisioikonomikisdiaxeirisis;
    }

    public function set_aitisioikonomikisdiaxeirisis($_aitisioikonomikisdiaxeirisis) {
        $this->_aitisioikonomikisdiaxeirisis = $_aitisioikonomikisdiaxeirisis;
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
        if($owner == null || $owner instanceof Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou) {
            $this->set_aitisioikonomikisdiaxeirisis($owner);
        }
    }
}
?>
