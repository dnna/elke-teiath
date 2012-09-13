<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="roles")
 */
class Application_Model_UserRole extends Dnna_Model_Object {
    /** @Id @Column(name="roleid", type="integer") */
    protected $_roleid;
    /** @Column(name="rolename", type="string") */
    protected $_rolename;

    public function get_roleid() {
        return $this->_roleid;
    }

    public function set_roleid($_roleid) {
        $this->_roleid = $_roleid;
    }

    public function get_rolename() {
        return $this->_rolename;
    }

    public function set_rolename($_rolename) {
        $this->_rolename = $_rolename;
    }
    
    public function get_id() {
        return $this->get_roleid();
    }
    
    public function get_name() {
        return $this->get_name();
    }
}
?>