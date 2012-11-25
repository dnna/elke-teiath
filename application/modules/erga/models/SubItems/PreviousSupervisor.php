<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.previoussupervisors")
 */
class Erga_Model_SubItems_PreviousSupervisor extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectBasicDetails", inversedBy="_previoussupervisors")
     * @JoinColumn (name="basicdetailsid", referencedColumnName="basicdetailsid")
     */
    protected $_basicdetails;
    /**
     * @ManyToOne (targetEntity="Application_Model_User", cascade={"persist"})
     * @JoinColumn (name="userid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_user;
    /**
     * @Column (name="startdate", type="date")
     * @FormFieldLabel Ημερομηνία Έναρξης
     * @var EDateTime
     */
    protected $_startdate;
    /**
     * @Column (name="enddate", type="date")
     * @FormFieldLabel Ημερομηνία Λήξης
     * @var EDateTime
     */
    protected $_enddate;

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

    public function get_startdate() {
        return $this->_startdate;
    }

    public function set_startdate($_startdate) {
        $this->_startdate = EDateTime::create($_startdate);
    }

    public function get_enddate() {
        return $this->_enddate;
    }

    public function set_enddate($_enddate) {
        if($_enddate != '') {
            $this->_enddate = EDateTime::create($_enddate);
        } else {
            $this->_enddate = null;
        }
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectBasicDetails) {
            $this->set_basicdetails($owner);
        }
    }
}
?>