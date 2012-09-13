<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.modifications")
 */
class Erga_Model_SubItems_Modification extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectBasicDetails", inversedBy="_modifications")
     * @JoinColumn (name="basicdetailsid", referencedColumnName="basicdetailsid")
     */
    protected $_basicdetails;
    /** @Column (name="refnum", type="string") */
    protected $_refnum;
    
    public function get_basicdetails() {
        return $this->_basicdetails;
    }

    public function set_basicdetails($_basicdetails) {
        $this->_basicdetails = $_basicdetails;
    }

    public function get_refnum() {
        return $this->_refnum;
    }

    public function set_refnum($_refnum) {
        $this->_refnum = $_refnum;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectBasicDetails) {
            $this->set_basicdetails($owner);
        }
    }
}
?>