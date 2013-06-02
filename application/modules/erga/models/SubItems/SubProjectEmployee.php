<?php

use Doctrine\Common\Collections\Criteria;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_SubProjectEmployees") @Table(name="elke_erga.employees")
 */
class Erga_Model_SubItems_SubProjectEmployee extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubProject", inversedBy="_employees")
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     */
    protected $_subproject;
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project", inversedBy="_employees")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     */
    protected $_project;
    /**
     * @ManyToOne (targetEntity="Application_Model_Employee", cascade={"persist"})
     * @JoinColumn (name="afm", referencedColumnName="afm")
     * @var Application_Model_Employee
     */
    protected $_employee;
    /** @Column (name="refnumapproved", type="string") */
    protected $_refnumapproved = ""; // Απόφαση Έγκρισης ΕΕΕ
    /** @Column (name="contractnum", type="string") */
    protected $_contractnum = ""; // Αριθμός Σύμβασης
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
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_Author", mappedBy="_employee")
     * @var Erga_Model_SubItems_Author
     */
    protected $_isauthor;
    // Φύλλα Χρονοχρέωσης
    /**
     * @OneToMany (targetEntity="Timesheets_Model_Timesheet", mappedBy="_employee")
     * @var ArrayCollection
     */
    protected $_timesheets;

    public function __construct(array $options = null) {
        $this->_isauthor = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($options);
    }

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject($_subproject) {
        if(isset($_subproject) && isset($this->_project)) {
            throw new Exception('Ο ίδιος απασχολούμενος δεν μπορεί να ανήκει ταυτόχρονα σε υποέργο αλλά και έργο.');
        }
        $this->_subproject = $_subproject;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        if(isset($_project) && isset($this->_subproject)) {
            throw new Exception('Ο ίδιος απασχολούμενος δεν μπορεί να ανήκει ταυτόχρονα σε έργο αλλά και υποέργο.');
        }
        $this->_project = $_project;
    }

    public function getProjectName() {
        if(isset($this->_project)) {
            return $this->_project->__toString();
        } else if(isset($this->_subproject)) {
            return $this->_subproject->__toString();
        } else {
            throw new Exception('Η συγκεκριμένη σύμβαση δεν έχει συνδεθεί ούτε με έργο ούτε με υποέργο!');
        }
    }
    
    /**
     * Επιστρέφει το αν η συγκεκριμένη σύμβαση ανήκει σε ενεργό έργο/υποέργο 
     */
    public function wasActive($month = null, $year = null) {
        if(!isset($month)) {
            $month = 12;
        }
        if(!isset($year)) {
            $year = date('Y');
        }
        if(isset($this->_project)) {
            $completiondate = $this->_project->getCompletionDate();
        } else if(isset($this->_subproject)) {
            $completiondate = $this->_subproject->getCompletionDate();
        } else {
            throw new Exception('Η συγκεκριμένη σύμβαση δεν έχει συνδεθεί ούτε με έργο ούτε με υποέργο!');
        }

        if($completiondate == null || ($completiondate->format('n') < $month && $completiondate->format('Y') < $year)) {
            return true;
        } else {
            return false;
        }
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

    public function get_amountAsFloat() {
        return Zend_Locale_Format::getNumber($this->get_amount(),
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       );
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

    public function get_isauthor() {
        return $this->_isauthor;
    }

    public function set_isauthor($_isauthor) {
        $this->_isauthor = $_isauthor;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function get_timesheets() {
        return $this->_timesheets;
    }

    public function set_timesheets($_timesheets) {
        $this->_timesheets = $_timesheets;
    }

    public function get_timesheetsApproved($year = null) {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq("_approved", Timesheets_Model_Timesheet::APPROVED))
            ->orderBy(array('_year' => 'ASC', '_month' => 'ASC'))
        ;
        if(isset($year)) {
            $criteria->andWhere(Criteria::expr()->eq("_year", $year));
        }
        return $this->_timesheets->matching($criteria)->toArray();
    }

    public function get_afm() {
        return $this->_employee->get_afm();
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubProject) { // Το condition υπάρχει για να μην μπαίνει author σαν owner
            $this->set_subproject($owner);
        } else if($owner == null || $owner instanceof Erga_Model_Project) { // Το condition υπάρχει για να μην μπαίνει author σαν owner
            $this->set_project($owner);
        }
    }

    static function compareEmployees(Erga_Model_SubItems_SubProjectEmployee $a, Erga_Model_SubItems_SubProjectEmployee $b)
    {
        // Step1 compare subprojects
        if($a->get_subproject() != null) {
            $al = $a->get_subproject()->get_subprojectnumber();
        } else {
            $a1 = '';
        }
        if($b->get_subproject() != null) {
            $bl = $b->get_subproject()->get_subprojectnumber();
        } else {
            $bl = '';
        }
        if ($al == $bl) {
            // Step2 compare names
            $al = strtolower($a->get_employee()->get_name());
            $bl = strtolower($b->get_employee()->get_name());
            if ($al == $bl) {
                // Step3 compare start dates
                if($a->get_startdate() == null) {
                    return 1;
                } else if($b->get_startdate() == null) {
                    return -1;
                } else if($a->get_startdate() == $b->get_startdate()) {
                    return 0;
                }
                return ($a->get_startdate() > $b->get_startdate()) ? +1 : -1;
            }
        }
        return ($al > $bl) ? +1 : -1;
    }

    public function __toString() {
        return $this->get_employee()->__toString();
    }
}
?>