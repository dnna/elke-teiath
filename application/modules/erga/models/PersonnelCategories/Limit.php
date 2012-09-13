<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.deliverablelimits")
 */
class Erga_Model_PersonnelCategories_Limit extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_Deliverable", inversedBy="_limits")
     * @JoinColumn (name="deliverableid", referencedColumnName="recordid")
     */
    protected $_deliverable;
    /**
     * @ManyToOne (targetEntity="Erga_Model_PersonnelCategories_PersonnelCategory")
     * @JoinColumn (name="personnelcategoryid", referencedColumnName="recordid")
     */
    protected $_personnelcategory;
    /** @Column (name="amountlimit", type="greekfloat") */
    protected $_limit;

    public function get_deliverable() {
        return $this->_deliverable;
    }

    public function set_deliverable($_deliverable) {
        $this->_deliverable = $_deliverable;
    }

    public function get_personnelcategory() {
        return $this->_personnelcategory;
    }

    public function set_personnelcategory($_personnelcategory) {
        $this->_personnelcategory = $_personnelcategory;
    }

    public function get_limit() {
        return $this->_limit;
    }

    public function set_limit($_limit) {
        $this->_limit = $_limit;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubItems_Deliverable) {
            $this->set_deliverable($owner);
        }
    }
}

?>