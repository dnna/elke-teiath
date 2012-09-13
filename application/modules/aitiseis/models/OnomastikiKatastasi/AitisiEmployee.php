<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_AitisiEmployees") @Table(name="elke_aitiseis.oka_employees")
 */
class Aitiseis_Model_OnomastikiKatastasi_AitisiEmployee extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_OnomastikiKatastasi", inversedBy="_employees")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_OnomastikiKatastasi
     */
    protected $_aitisi;
    /**
     * @ManyToOne (targetEntity="Application_Model_Employee", cascade={"persist"})
     * @JoinColumn (name="afm", referencedColumnName="afm")
     * @var Application_Model_Employee
     */
    protected $_employee;
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
    /** @Column (name="manmonths", type="greekfloat") */
    protected $_manmonths; // Ανθρωπομήνες
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount; // Ποσό Σύμβασης (με ΦΠΑ)
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_EmployeeCategory", cascade={"persist"})
     * @JoinColumn (name="categoryid", referencedColumnName="id")
     * @var Application_Model_Lists_EmployeeCategory
     */
    protected $_category;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_EmployeeSpecialty", cascade={"persist"})
     * @JoinColumn (name="specialtyid", referencedColumnName="id")
     * @var Application_Model_Lists_EmployeeSpecialty
     */
    protected $_specialty; // Ειδικότητα στο έργο
    /** @Column (name="comments", type="string") */
    protected $_comments;

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }

    public function get_employee() {
        return $this->_employee;
    }

    public function set_employee(Application_Model_Employee $_employee) {
        if(isset($this->_employee)) {
            $oldemployee = $this->_employee;
        }
        $this->_employee = $_employee;
        // Ελέγχουμε αν ο παλαιός employee έχει ίδιο ΑΦΜ με τον νέο. Αν όχι τότε κάνουμε κάτι σαν garbage collection.
        // TODO να μην εκτελείται πάντα αλλά με κάποια πιθανότητα
        if(isset($oldemployee) && $oldemployee->get_afm() !== $_employee->get_afm()) {
            $em = Zend_Registry::get('entityManager');
            $em->getRepository('Application_Model_Employee')->garbageCollection();
        }
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

    public function get_manmonths() {
        return $this->_manmonths;
    }

    public function set_manmonths($_manmonths) {
        $this->_manmonths = $_manmonths;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }

    public function get_category() {
        return $this->_category;
    }

    public function set_category($_category) {
        $this->_category = $_category;
    }

    public function get_specialty() {
        return $this->_specialty;
    }

    public function set_specialty($_specialty) {
        $this->_specialty = $_specialty;
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Aitiseis_Model_OnomastikiKatastasi) { // Το condition υπάρχει για να μην μπαίνει author σαν owner
            $this->set_aitisi($owner);
        }
    }
}
?>