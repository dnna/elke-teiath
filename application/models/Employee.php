<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Employees") @Table(name="elke.employees")
 */
class Application_Model_Employee extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="afm", type="string")
     */
    protected $_afm; // Α.Φ.Μ.
    /** @Column (name="doy", type="string") */
    protected $_doy; // Δ.Ο.Υ.
    /** @Column (name="adt", type="string") */
    protected $_adt; // Α.Δ.Τ.
    /**
     * @Column (name="firstname", type="string")
     */
    protected $_firstname;
    /**
     * @Column (name="surname", type="string")
     */
    protected $_surname;
    /** @Column (name="address", type="string") */
    protected $_address; // Διεύθυνση Κατοικίας (οδός, αριθμός, ΤΚ, πόλη)
    /** @Column (name="email", type="string") */
    protected $_email; // Email
    /** @Column (name="phone", type="string") */
    protected $_phone; // Τηλέφωνο
    /** @Column (name="ldapusername", type="string") */
    protected $_ldapusername = "";
    /** @Column (name="maxhours", type="string") */
    protected $_maxhours = 0;
    // Για τα object loops
    protected $_name;
    ///**
     //* @OneToMany (targetEntity="Erga_Model_SubItems_SubProjectEmployee", mappedBy="_employee")
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

    public function get_adt() {
        return $this->_adt;
    }

    public function set_adt($_adt) {
        $this->_adt = $_adt;
    }

    public function get_firstname() {
        return $this->_firstname;
    }

    public function set_firstname($_firstname) {
        if($_firstname != "") {
            $this->_firstname = $_firstname;
        } else {
            throw new Exception('Το όνομα του απασχολούμενου δεν μπορεί να είναι κενό.');
        }
    }

    public function get_surname() {
        return $this->_surname;
    }

    public function set_surname($_surname) {
        if($_surname != "") {
            $this->_surname = $_surname;
        } else {
            throw new Exception('Το επώνυμο του απασχολούμενου δεν μπορεί να είναι κενό.');
        }
    }

    public function get_name() {
        return $this->get_surname().' '.$this->get_firstname();
    }

    public function get_address() {
        return $this->_address;
    }

    public function set_address($_address) {
        $this->_address = $_address;
    }

    public function get_ldapusername() {
        return $this->_ldapusername;
    }

    public function set_ldapusername($_ldapusername) {
        $this->_ldapusername = $_ldapusername;
    }

    public function get_maxhours() {
        return $this->_maxhours;
    }

    public function set_maxhours($_maxhours) {
        $this->_maxhours = $_maxhours;
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