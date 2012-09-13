<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_aitiseis.ep_deliverables")
 */
class Aitiseis_Model_EntoliPliromis_Deliverable extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_EntoliPliromis", inversedBy="_deliverables")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     */
    protected $_entolipliromis;
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_Deliverable")
     * @JoinColumn (name="deliverableid", referencedColumnName="recordid")
     */
    protected $_deliverable;
    /** @Column (name="comments", type="string") */
    protected $_comments;

    public function get_entolipliromis() {
        return $this->_entolipliromis;
    }

    public function set_entolipliromis($_entolipliromis) {
        $this->_entolipliromis = $_entolipliromis;
    }

    public function get_deliverable() {
        return $this->_deliverable;
    }

    public function set_deliverable($_deliverable) {
        $this->_deliverable = $_deliverable;
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function __toString() {
        return $this->get_deliverable()->__toString();
    }
    
    public function setOwner($owner) {
        if($owner instanceof Aitiseis_Model_EntoliPliromis) {
            $this->set_entolipliromis($owner);
        }
    }
}
?>