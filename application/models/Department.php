<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke.departments")
 */
class Application_Model_Department extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="department_id", type="integer")
     */
    protected $_id;
    /**
     * @Column (name="school_id", type="integer")
     */
    protected $_school;
    /**
     * @Column (name="name", type="string")
     */
    protected $_name;
    /**
     * @Column (name="lname", type="string")
     */
    protected $_lname;
    /**
     * @Column (name="gramm_id", type="integer")
     */
    protected $_gramm;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_school() {
        return $this->_school;
    }

    public function set_school($_school) {
        $this->_school = $_school;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
    }

    public function get_lname() {
        return $this->_lname;
    }

    public function set_lname($_lname) {
        $this->_lname = $_lname;
    }

    public function get_gramm() {
        return $this->_gramm;
    }

    public function set_gramm($_gramm) {
        $this->_gramm = $_gramm;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>