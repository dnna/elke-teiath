<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.committee")
 */
class Erga_Model_SubItems_CommitteeMember extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectBasicDetails", inversedBy="_committee")
     * @JoinColumn (name="basicdetailsid", referencedColumnName="basicdetailsid")
     */
    protected $_basicdetails;
    /**
     * @ManyToOne (targetEntity="Application_Model_User", cascade={"persist"})
     * @JoinColumn (name="userid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_user;

    public function get_basicdetails() {
        return $this->_basicdetails;
    }

    public function set_basicdetails($_basicdetails) {
        $this->_basicdetails = $_basicdetails;
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

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectBasicDetails) {
            $this->set_basicdetails($owner);
        }
    }
}
?>