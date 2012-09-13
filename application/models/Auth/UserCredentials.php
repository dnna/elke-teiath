<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="user_credentials")
 */
class Application_Model_Auth_UserCredentials extends Dnna_Model_Object {
    /** @Id @Column(name="userid", type="integer") */
    protected $_userid;
    /** @Column(name="username", type="string") */
    protected $_username;
    /** @Column(name="password", type="string") */
    protected $_password;

    public function get_userid() {
        return $this->_userid;
    }

    public function set_userid($_userid) {
        $this->_userid = $_userid;
    }

    public function get_username() {
        return $this->_username;
    }

    public function set_username($_username) {
        $this->_username = $_username;
    }

    public function get_password() {
        return $this->_password;
    }

    public function set_password($_password) {
        $this->_password = $_password;
    }
}
?>