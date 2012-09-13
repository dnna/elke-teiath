<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_aitiseis.aode_partners")
 */
class Aitiseis_Model_OikonomikiDiaxeirisi_Ergou_Partner extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_OikonomikiDiaxeirisi_Ergou", inversedBy="_partners")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     */
    protected $_aitisi;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency")
     * @JoinColumn (name="partnerlistitemid", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_partnerlistitem;

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }

    public function get_partnerlistitem() {
        return $this->_partnerlistitem;
    }

    public function set_partnerlistitem($_partnerlistitem) {
        $this->_partnerlistitem = $_partnerlistitem;
    }
    
    public function setOwner($owner) {
        if($owner == null || $owner instanceof Aitiseis_Model_OikonomikiDiaxeirisi_Ergou) {
            $this->set_aitisi($owner);
        }
    }
}
?>