<?php
/**
 * Γενική κλάση την οποία κληρονομούν τα αντικείμενα του model.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
abstract class Application_Model_SubObject extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="recordid", type="integer")
     * @GeneratedValue
     */
    protected $_recordid;

    abstract public function setOwner($owner);

    public function get_recordid() {
        return $this->_recordid;
    }

    public function set_recordid($_recordid) {
        $this->_recordid = $_recordid;
    }
}
?>