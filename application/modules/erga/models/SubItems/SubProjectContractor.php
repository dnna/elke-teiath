<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_SubProjectContractors") @Table(name="elke_erga.contractors")
 */
class Erga_Model_SubItems_SubProjectContractor extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubProject", inversedBy="_contractors")
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     */
    protected $_subproject;
    /**
     * @ManyToOne (targetEntity="Application_Model_Contractor", inversedBy="_contracts", cascade={"persist"})
     * @JoinColumn (name="contractorafm", referencedColumnName="afm")
     * @var Application_Model_Contractor
     */
    protected $_agency;
    /** @Column (name="refnumapproved", type="string") */
    protected $_refnumapproved = ""; // Απόφαση Έγκρισης ΕΕΕ
    /** @Column (name="contractnum", type="string") */
    protected $_contractnum = ""; // Αριθμός Σύμβασης
    /**
     * @Column (name="contact", type="string")
     */
    protected $_contact;
    /**
     * @Column (name="phone", type="string")
     */
    protected $_phone;
    /**
     * @Column (name="email", type="string")
     */
    protected $_email;
    /**
     * @Column (name="startdate", type="date")
     * @var EDateTime
     */
    protected $_startdate; // Αρχή σύμβασης
    /**
     * @Column (name="enddate", type="date")
     * @var EDateTime
     */
    protected $_enddate; // Τέλος σύμβασης
    /** @Column (name="refnumcontract", type="string") */
    protected $_refnumcontract; // Αρ. Πρωτ. Σύμβασης
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount; // Ποσό Σύμβασης (με ΦΠΑ)
    /**
     * @Column (name="provisionalacceptancedate", type="date")
     * @var EDateTime
     */
    protected $_provisionalacceptancedate; // Ημ/νία Προσωρινής Παραλαβής
    /**
     * @Column (name="finalacceptancedate", type="date")
     * @var EDateTime
     */
    protected $_finalacceptancedate; // Ημ/νία Οριστικής Παραλαβής
    /**
     * @Column (name="repaymentdate", type="date")
     * @var EDateTime
     */
    protected $_repaymentdate; // Ημ/νία Αποπληρωμής
    /** @Column (name="comments", type="string") */
    protected $_comments;

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject($_subproject) {
        $this->_subproject = $_subproject;
    }

    public function get_agency() {
        return $this->_agency;
    }

    public function set_agency(Application_Model_Contractor $_agency) {
        if(isset($this->_agency)) {
            $oldagency = $this->_agency;
        }
        $this->_agency = $_agency;
        // Ελέγχουμε αν ο παλαιός contractor έχει ίδιο ΑΦΜ με τον νέο. Αν όχι τότε κάνουμε κάτι σαν garbage collection.
        // TODO να μην εκτελείται πάντα αλλά με κάποια πιθανότητα
        if(isset($oldagency) && $oldagency->get_afm() !== $_agency->get_afm()) {
            $em = Zend_Registry::get('entityManager');
            $em->getRepository('Application_Model_Contractor')->garbageCollection();
        }
    }

    public function get_refnumapproved() {
        return $this->_refnumapproved;
    }

    public function set_refnumapproved($_refnumapproved) {
        $this->_refnumapproved = $_refnumapproved;
    }

    public function get_contractnum() {
        return $this->_contractnum;
    }

    public function set_contractnum($_contractnum) {
        $this->_contractnum = $_contractnum;
    }

    public function get_contact() {
        return $this->_contact;
    }

    public function set_contact($_contact) {
        $this->_contact = $_contact;
    }

    public function get_phone() {
        return $this->_phone;
    }

    public function set_phone($_phone) {
        $this->_phone = $_phone;
    }

    public function get_email() {
        return $this->_email;
    }

    public function set_email($_email) {
        $this->_email = $_email;
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
        $this->_enddate = EDateTime::create($_enddate);
    }

    public function get_refnumcontract() {
        return $this->_refnumcontract;
    }

    public function set_refnumcontract($_refnumcontract) {
        $this->_refnumcontract = $_refnumcontract;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }

    public function get_provisionalacceptancedate() {
        return $this->_provisionalacceptancedate;
    }

    public function set_provisionalacceptancedate($_provisionalacceptancedate) {
        $this->_provisionalacceptancedate = EDateTime::create($_provisionalacceptancedate);
    }

    public function get_finalacceptancedate() {
        return $this->_finalacceptancedate;
    }

    public function set_finalacceptancedate($_finalacceptancedate) {
        $this->_finalacceptancedate = EDateTime::create($_finalacceptancedate);
    }

    public function get_repaymentdate() {
        return $this->_repaymentdate;
    }

    public function set_repaymentdate($_repaymentdate) {
        $this->_repaymentdate = EDateTime::create($_repaymentdate);
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    /**
     * Επιστρέφει την φάση στην οποία είναι ο ανάδοχος.
     * @return int Η φάση που βρίσκεται ο ανάδοχος αριθμητικά
     */
    public function get_contractstage() {
        $now = new EDateTime('now');
        if($this->get_repaymentdate() != null && $this->get_repaymentdate() <= $now) {
            return '3'; // Αποπληρωμής
        } else if($this->get_finalacceptancedate() != null && $this->get_finalacceptancedate() <= $now) {
            return '2'; // Οριστικής Παραλαβής
        } else if($this->get_provisionalacceptancedate() != null && $this->get_provisionalacceptancedate() <= $now) {
            return '1'; // Προσωρινής Παραλαβής
        } else {
            return '0'; // Πριν την προσωρινή παραλαβή
        }
    }

    public function get_afm() {
        return $this->_agency->get_afm();
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubProject) { // Το condition υπάρχει για να μην μπαίνει author σαν owner
            $this->set_subproject($owner);
        }
    }

    public function __toString() {
        return $this->get_agency()->__toString();
    }
}
?>
