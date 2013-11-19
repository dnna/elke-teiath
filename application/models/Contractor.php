<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Contractors") @Table (name="contractors")
 */
class Application_Model_Contractor extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="afm", type="string")
     */
    protected $_afm; // Α.Φ.Μ.
    /** @Column (name="doy", type="string") */
    protected $_doy; // Δ.Ο.Υ.
    /**
     * @Column (name="name", type="string")
     * @FormFieldLabel Επωνυμία
     * @FormFieldRequired
     * @IsKeyElement
     */
    protected $_name;
    /**
     * @Column (name="address", type="string")
     * @FormFieldLabel Διεύθυνση
     */
    protected $_address;
    /**
     * @Column (name="type", type="string")
     * @FormFieldLabel Είδος Φορέα
     * @FormFieldType SimpleSelect
     * @FormFieldOptions Δημόσιος, Ιδιωτικός, Ευρωπαϊκό Πρόγραμμα, Ίδιοι Πόροι, Αυτοχρηματοδότηση
     * @FormFieldRequired
     */
    protected $_type;
    ///**
     //* @OneToMany (targetEntity="Erga_Model_SubItems_SubProjectContractor", mappedBy="_agency")
     //*/
    //protected $_contracts; // Συμβάσεις

    public function get_afm() {
        return $this->_afm;
    }

    public function set_afm($_afm) {
        $this->_afm = $_afm;
    }

    public function get_doy() {
        return $this->_doy;
    }

    public function set_doy($_doy) {
        $this->_doy = $_doy;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        if($_name != "") {
            $this->_name = $_name;
        } else {
            throw new Exception('Η επωνυμία του αναδόχου δεν μπορεί να είναι κενή.');
        }
    }

    public function get_address() {
        return $this->_address;
    }

    public function set_address($_address) {
        $this->_address = $_address;
    }

    public function get_type() {
        return $this->_type;
    }

    public function set_type($_type) {
        $this->_type = $_type;
    }

    public function get_contracts() {
        return $this->_contracts;
    }

    public function set_contracts($_contracts) {
        $this->_contracts = $_contracts;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>