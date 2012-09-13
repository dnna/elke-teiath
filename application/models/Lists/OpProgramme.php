<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Lists") @Table(name="opprogrammes")
 */
class Application_Model_Lists_OpProgramme extends Dnna_Model_Object implements Application_Model_Lists_ListInterface {
    /**
     * @Id
     * @Column (name="opprogrammeid", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_opprogrammeid;
    /**
     * @Column (name="opprogrammename", type="string")
     * @FormFieldLabel Επιχειρησιακό Πρόγραμμα
     * @FormFieldRequired
     * @IsKeyElement
     * @NullValue -
     */
    protected $_opprogrammename;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_FundingFramework", inversedBy="_opprogrammes", cascade={"persist"})
     * @JoinColumn (name="fundingframeworkid", referencedColumnName="fundingframeworkid")
     * @FormFieldLabel Πλαίσιο Χρηματοδότησης
     * @FormFieldType ParentSelect
     * @FormFieldRequired
     * @var Application_Model_Lists_FundingFramework
     */
    protected $_fundingframework;

    public function get_id() {
        return $this->get_opprogrammeid();
    }

    public function get_name() {
        return $this->get_opprogrammename();
    }

    public function get_opprogrammeid() {
        return $this->_opprogrammeid;
    }

    public function set_opprogrammeid($_opprogrammeid) {
        $this->_opprogrammeid = $_opprogrammeid;
    }

    public function get_opprogrammename() {
        return $this->_opprogrammename;
    }

    public function set_opprogrammename($_opprogrammename) {
        $this->_opprogrammename = $_opprogrammename;
    }

    public function get_fundingframework() {
        return $this->_fundingframework;
    }

    public function set_fundingframework($_fundingframework) {
        $this->_fundingframework = $_fundingframework;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>