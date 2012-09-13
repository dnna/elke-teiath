<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_praktika.committeemembers")
 */
class Praktika_Model_Committee_Member extends Application_Model_SubObject {
    const CAPACITY_MEMBER = 1;
    const CAPACITY_CHAIRMAN = 2;

    /**
     * @ManyToOne (targetEntity="Praktika_Model_CommitteeBase", inversedBy="_committeemembers")
     * @JoinColumn (name="committeeid", referencedColumnName="id")
     */
    protected $_committee;
    /**
     * @ManyToOne (targetEntity="Application_Model_User", cascade={"persist"})
     * @JoinColumn (name="userid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_user;
    /**
     * @Column (name="capacity", type="integer")
     */
    protected $_capacity = self::CAPACITY_MEMBER;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_committee() {
        return $this->_committee;
    }

    public function set_committee($_committee) {
        $this->_committee = $_committee;
    }

    public function get_user() {
        if($this->_user->get_realname() == null) {
            $this->_user->setOptions(Application_Model_QueryHelpers_Users::findUserById($this->_user->get_userid())->getOptions());
        }
        return $this->_user;
    }

    public function set_user($_user) {
        $this->_user = $_user;
    }

    public function get_capacity() {
        return $this->_capacity;
    }

    public function set_capacity($_capacity) {
        $this->_capacity = $_capacity;
    }

    public function getCapacityText() {
        $capacities = self::getCapacities();
        return $capacities[$this->_capacity];
    }

    public static function getCapacities() {
        return array(self::CAPACITY_MEMBER => 'Μέλος', self::CAPACITY_CHAIRMAN => 'Πρόεδρος');
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Praktika_Model_CommitteeBase) {
            $this->set_committee($owner);
        }
    }

    public function __toString() {
        $name = $this->get_user()->__toString();
        if($this->_capacity == self::CAPACITY_CHAIRMAN) {
            $name = '(Π) '.$name;
        }
        return $name;
    }
}
?>