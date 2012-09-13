<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.projectposition")
 */
class Erga_Model_ProjectPosition extends Dnna_Model_Object {
    // Επιπλέον στοιχεία έργου
    /**
     * @Id
     * @Column (name="positionid", type="integer")
     * @GeneratedValue
     */
    protected $_positionid;
    /**
     * @OneToOne (targetEntity="Erga_Model_Project", inversedBy="_position")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     */
    protected $_project;
    /**
     * @Column (name="teirole", type="integer")
     */
    protected $_teirole = 1;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_Partner", mappedBy="_position", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_Partner
     */
    protected $_partners; // Συνεργαζόμενοι φορείς
    /**
     * @Column (name="teiiscoordinator", type="integer")
     * var bool
     */
    protected $_teiiscoordinator = 0; // Συντονιστής φορέας
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency")
     * @JoinColumn (name="anadoxosid", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_anadoxos;

    protected $_isvirtual = 0;
    
    public function get_positionid() {
        return $this->_positionid;
    }

    public function set_positionid($_positionid) {
        $this->_positionid = $_positionid;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }

    public function get_teirole() {
        return $this->_teirole;
    }

    public function set_teirole($_teirole) {
        $this->_teirole = $_teirole;
    }

    public function get_partners() {
        return $this->_partners;
    }

    public function set_partners($_partners) {
        $this->_partners = $_partners;
    }

    public function get_teiiscoordinator() {
        return $this->_teiiscoordinator;
    }

    public function set_teiiscoordinator($_teiiscoordinator) {
        $this->_teiiscoordinator = $_teiiscoordinator;
    }

    public function get_anadoxos() {
        return $this->_anadoxos;
    }

    public function set_anadoxos($_anadoxos) {
        $this->_anadoxos = $_anadoxos;
    }

    public function get_isvirtual() {
        return $this->_isvirtual;
    }

    public function set_isvirtual($_isvirtual) {
        $this->_isvirtual = $_isvirtual;
    }

    public function setOwner($owner) {
        $this->set_project($owner);
    }
}
?>