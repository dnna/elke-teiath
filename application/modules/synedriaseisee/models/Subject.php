<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Synedriaseisee_Model_Repositories_Subjects") @Table(name="elke_synedriaseisee.subjects")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="subjecttype", type="string")
 * @DiscriminatorMap({"subject" = "Synedriaseisee_Model_Subject", "aitisisubject" = "Synedriaseisee_Model_AitisiSubject", "simplesubject" = "Synedriaseisee_Model_SimpleSubject"})
 */
abstract class Synedriaseisee_Model_Subject extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Synedriaseisee_Model_Synedriasi", inversedBy="_subjects")
     * @JoinColumn (name="synedriasiid", referencedColumnName="id")
     * @var Synedriaseisee_Model_Synedriasi
     */
    protected $_synedriasi;
    /**
     * @Column (name="num", type="integer")
     */
    protected $_num;

    protected $_titlewithnum;

    public function get_id() {
        return $this->get_recordid();
    }

    public function get_synedriasi() {
        return $this->_synedriasi;
    }

    public function set_synedriasi(Synedriaseisee_Model_Synedriasi $_synedriasi = null) {
        $this->_synedriasi = $_synedriasi;
    }

    public function get_num() {
        return $this->_num;
    }

    public function set_num($_num) {
        $this->_num = $_num;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Synedriaseisee_Model_Synedriasi) {
            $this->set_synedriasi($owner);
        }
    }

    public function get_titlewithnum() {
        return $this->get_num().'. '.$this->get_rawtitle();
    }

    public function __toString() {
        return $this->get_titlewithnum();
    }

    abstract function get_rawtitle();
}
?>