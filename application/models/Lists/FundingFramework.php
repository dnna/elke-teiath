<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Lists") @Table(name="fundingframeworks")
 */
class Application_Model_Lists_FundingFramework extends Dnna_Model_Object implements Application_Model_Lists_ListInterface {
    /**
     * @Id
     * @Column (name="fundingframeworkid", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_fundingframeworkid;
    /**
     * @Column (name="fundingframeworkname", type="string")
     * @FormFieldLabel Πλαίσιο Χρηματοδότησης
     * @FormFieldRequired
     * @IsKeyElement
     * @NullValue -
     */
    protected $_fundingframeworkname;
    /**
     * @OneToMany (targetEntity="Application_Model_Lists_OpProgramme", mappedBy="_fundingframework", cascade={"all"})
     * @var ArrayCollection
     */
    protected $_opprogrammes;

    public function get_id() {
        return $this->get_fundingframeworkid();
    }

    public function get_name() {
        return $this->get_fundingframeworkname();
    }

    public function get_fundingframeworkid() {
        return $this->_fundingframeworkid;
    }

    public function set_fundingframeworkid($_fundingframeworkid) {
        $this->_fundingframeworkid = $_fundingframeworkid;
    }

    public function get_fundingframeworkname() {
        return $this->_fundingframeworkname;
    }

    public function set_fundingframeworkname($_fundingframeworkname) {
        $this->_fundingframeworkname = $_fundingframeworkname;
    }

    public function get_opprogrammes() {
        return $this->_opprogrammes;
    }

    public function set_opprogrammes($_opprogrammes) {
        $this->_opprogrammes = $_opprogrammes;
    }
    
    public function __toString() {
        return $this->get_name();
    }
}
?>