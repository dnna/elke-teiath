<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.projectbasicdetails")
 */
class Erga_Model_ProjectBasicDetails extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="basicdetailsid", type="integer")
     * @GeneratedValue
     */
    protected $_basicdetailsid;
    /**
     * @OneToOne (targetEntity="Erga_Model_Project", inversedBy="_basicdetails")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     */
    protected $_project;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_ProjectCategory")
     * @JoinColumn (name="category", referencedColumnName="id")
     * @var Application_Model_Lists_ProjectCategory
     */
    protected $_category;
    /** @Column (name="mis", type="string") */
    protected $_mis; // MIS
    /** @Column (name="acccode", type="string") */
    protected $_acccode; // Κωδικός Λογιστηρίου
    /**
     * @ManyToOne (targetEntity="Application_Model_Department", cascade={"persist"})
     * @JoinColumn (name="departmentid", referencedColumnName="department_id")
     * @var Application_Model_Department
     */
    protected $_department;
    /** @Column (name="refnumapproved", type="string") */
    protected $_refnumapproved = ""; // Απόφαση Έγκρισης ΕΕΕ
    /** @Column (name="refnumstart", type="string") */
    protected $_refnumstart = ""; // Αρ. Πρωτ. Ένταξης
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_Modification", mappedBy="_basicdetails", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_Modification
     */
    protected $_modifications; // Πίνακας με Αρ. Πρωτ. Τροποποιήσεων
    /**
     * @Column (name="title", type="string")
     * @FormFieldLabel Τίτλος
     */
    protected $_title = "--NONAME--";
    /**
     * @Column (name="titleen", type="string")
     * @FormFieldLabel Τίτλος (στα Αγγλικά)
     */
    protected $_titleen = ""; // Ο τίτλος στα Αγγλικά
    /**
     * @ManyToOne (targetEntity="Application_Model_User", cascade={"persist"})
     * @JoinColumn (name="supervisoruserid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_supervisor;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_CommitteeMember", mappedBy="_basicdetails", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_CommitteeMember
     */
    protected $_committee; // Επιστημονική Επιτροπή Έργου
    /**
     * @Column (name="description", type="string")
     */
    protected $_description = "";
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
    /**
     * @Column (name="comments", type="string")
     */
    protected $_comments;

    protected $_isvirtual = 0;

    protected $__duration;
    
    public function __construct(array $options = null) {
        $this->set_startdate(new EDateTime('now'));
        $this->set_enddate(new EDateTime('now'));
        parent::__construct($options);
    }
    
    public function get_basicdetailsid() {
        return $this->_basicdetailsid;
    }

    public function set_basicdetailsid($_basicdetailsid) {
        $this->_basicdetailsid = $_basicdetailsid;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }

    public function get_category() {
        if($this->_category != null) {
            return $this->_category;
        } else {
            $category = new Application_Model_Lists_ProjectCategory();
            return $category;
        }
    }

    public function set_category($_category) {
        $this->_category = $_category;
    }

    public function get_mis() {
        return $this->_mis;
    }

    public function set_mis($_mis) {
        $this->_mis = $_mis;
    }

    public function get_acccode() {
        return $this->_acccode;
    }

    public function set_acccode($_acccode) {
        $this->_acccode = $_acccode;
    }

    public function get_department() {
        return $this->_department;
    }

    public function set_department($_department) {
        $this->_department = $_department;
    }

    public function get_refnumapproved() {
        return $this->_refnumapproved;
    }

    public function set_refnumapproved($_refnumapproved) {
        $this->_refnumapproved = $_refnumapproved;
    }

    public function get_refnumstart() {
        return $this->_refnumstart;
    }

    public function set_refnumstart($_refnumstart) {
        $this->_refnumstart = $_refnumstart;
    }

    public function get_modifications() {
        return $this->_modifications;
    }

    public function set_modifications($_modifications) {
        $this->_modifications = $_modifications;
    }

    public function get_title() {
        return $this->_title;
    }
    
    public function get_titleCondensed() {
        return str_replace(" ", "", $this->get_title());
    }

    public function set_title($_title) {
        $this->_title = $_title;
    }

    public function get_titleen() {
        return $this->_titleen;
    }

    public function set_titleen($_titleen) {
        $this->_titleen = $_titleen;
    }

    public function get_supervisor() {
        if($this->_supervisor != null) {
            return $this->_supervisor;
        } else {
            return new Application_Model_User();
        }
    }

    public function set_supervisor($_supervisor) {
        $this->_supervisor = $_supervisor;
    }

    public function get_committee() {
        return $this->_committee;
    }

    public function set_committee($_committee) {
        $this->_committee = $_committee;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
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

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function get_iscomplex() {
        return $this->get_project()->get_iscomplex();
    }

    public function get_isvirtual() {
        return $this->_isvirtual;
    }

    public function set_isvirtual($_isvirtual) {
        $this->_isvirtual = $_isvirtual;
    }

    public function get__duration() { // Το προεπιλεγμένο div θα χωρίσει το duration σε μήνες
        $days = $this->get_enddate()->diff($this->get_startdate())
                ->format("%a");
        return $days/30;
    }
    
    public function setOwner($owner) {
        $this->set_project($owner);
    }
}
?>