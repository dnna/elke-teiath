<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.partners")
 */
class Erga_Model_SubItems_Partner extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectPosition", inversedBy="_partners")
     * @JoinColumn (name="positionid", referencedColumnName="positionid")
     */
    protected $_position;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency")
     * @JoinColumn (name="partnerlistitemid", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_partnerlistitem;
    /** @Column (name="amount", type="greekfloat", nullable=true) */
    protected $_amount;
    /**
     * @Column (name="iscoordinator", type="integer")
     * var bool
     */
    protected $_iscoordinator;
    /**
     * @Column (name="partnercontact", type="string")
     */
    protected $_partnercontact;
    /**
     * @Column (name="partnerphone", type="string")
     */
    protected $_partnerphone;
    /**
     * @Column (name="partneremail", type="string")
     */
    protected $_partneremail;

    public function get_position() {
        return $this->_position;
    }

    public function set_position($_position) {
        $this->_position = $_position;
    }

    public function get_partnerid() {
        return $this->_partnerid;
    }

    public function set_partnerid($_partnerid) {
        $this->_partnerid = $_partnerid;
    }

    public function get_partnerlistitem() {
        return $this->_partnerlistitem;
    }

    public function set_partnerlistitem($_partnerlistitem) {
        $this->_partnerlistitem = $_partnerlistitem;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }
    
    public function get_iscoordinator() {
        return $this->_iscoordinator;
    }

    public function set_iscoordinator($_iscoordinator) {
        $this->_iscoordinator = $_iscoordinator;
    }

    public function get_partnercontact() {
        return $this->_partnercontact;
    }

    public function set_partnercontact($_partnercontact) {
        $this->_partnercontact = $_partnercontact;
    }

    public function get_partnerphone() {
        return $this->_partnerphone;
    }

    public function set_partnerphone($_partnerphone) {
        $this->_partnerphone = $_partnerphone;
    }

    public function get_partneremail() {
        return $this->_partneremail;
    }

    public function set_partneremail($_partneremail) {
        $this->_partneremail = $_partneremail;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectPosition) {
            $this->set_position($owner);
        }
    }
}
?>
