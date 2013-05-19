<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_Deliverables") @Table(name="elke_erga.deliverables")
 * @HasLifecycleCallbacks
 */
class Erga_Model_SubItems_Deliverable extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_WorkPackage", inversedBy="_deliverables", cascade={"persist"})
     * @JoinColumn (name="workpackageid", referencedColumnName="recordid")
     */
    protected $_workpackage; // Πακέτο Εργασίας
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_Author", mappedBy="_deliverable", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_Author
     */
    protected $_authors; // Συντάκτες (αν υπάρχουν)
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectContractor", cascade={"persist"})
     * @JoinColumn (name="contractorid", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_SubProjectContractor
     */
    protected $_contractor; // Ανάδοχος (αν υπάρχει)
    /** @Column (name="codename", type="string") */
    protected $_codename = "--NONAME--";
    /** @Column (name="title", type="string") */
    protected $_title = "--NONAME--";
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount;
    /**
     * @Column (name="startdate", type="date")
     * @var EDateTime
     */
    protected $_startdate;
    /**
     * @Column (name="enddate", type="date")
     * @var EDateTime
     */
    protected $_enddate;
    /**
     * @Column (name="assignmentapprovaldate", type="date")
     * @var EDateTime
     */
    protected $_assignmentapprovaldate;
    /**
     * @Column (name="completionapprovaldate", type="date")
     * @var EDateTime
     */
    protected $_completionapprovaldate;
    /**
     * @Column (name="comments", type="string")
     */
    protected $_comments; // Γενικές Παρατηρήσεις

    /**
     * @OneToMany (targetEntity="Timesheets_Model_Activity", mappedBy="_deliverable")
     * @var Timesheets_Model_Activity
     */
    protected $_timesheetactivities;
    /**
     * @OneToMany (targetEntity="Erga_Model_PersonnelCategories_Limit", mappedBy="_deliverable")
     * @var Erga_Model_PersonnelCategories_Limit
     */
    protected $_limits;

    protected $__duration;

    public function __construct($options = array()) {
        $this->_authors = new Doctrine\Common\Collections\ArrayCollection;
        parent::__construct($options);
    }

    public function get_workpackage() {
        return $this->_workpackage;
    }

    public function set_workpackage($_workpackage) {
        $this->_workpackage = $_workpackage;
    }

    public function get_codename() {
        return $this->_codename;
    }

    public function set_codename($_codename) {
        $this->_codename = $_codename;
    }

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
    }

    public function get_fulltitle() {
        return $this->get_codename().' '.$this->get_title();
    }

    public function get_shorttitle() {
        return $this->get_codename();
    }

    public function get_authors() {
        return $this->_authors;
    }

    public function set_authors($_authors) {
        $amountsum = 0;
        foreach($_authors as $curAuthor) {
            if($curAuthor->get_amountAsFloat() > $this->get_amountAsFloat()) {
                $exception = new Exception('Το πόσο του συντάκτη '.$curAuthor->get_employee()->__toString().' ξεπερνά τον προϋπολογισμό του παραδοτέου. Ο συγκεκριμένος συντάκτης δεν προστέθηκε.');
                $_authors->removeElement($curAuthor);
            } else {
                $amountsum = $amountsum + $curAuthor->get_amountAsFloat();
            }
        }
        if($amountsum > $this->get_amountAsFloat()) {
            throw new Exception('Το άθροισμα των ποσών των συντακτών ξεπερνά τον προϋπολογισμό του παραδοτέου. Παρακαλώ ξαναεισάγετε τους συντάκτες.');
        }
        $this->_authors = $this->modifySubCollection($_authors, $this->_authors);
        if(isset($exception)) {
            throw $exception;
        }
    }

    public function get_contractor() {
        return $this->_contractor;
    }

    public function set_contractor($_contractor) {
        $this->_contractor = $_contractor;
    }

    /**
     * Επιστρέφει έναν δισδιάστατο associative πίνακα όπου σαν κλειδιά
     * χρησιμοποιούνται τα id των συνακτών και σαν περιεχόμενα τα ονόματα
     * τους.
     * @return array Δισδιάστατος πίνακας με τα id και τα όνοματα των
     * συνατκών.
     */
    public function get_authorSurnamesAs2dArray() {
        $array = array();
        foreach($this->get_authors() as $curAuthor) {
            $array[$curAuthor->get_recordid()] = $curAuthor->get_employee()->get_employee()->get_surname();
        }
        return $array;
    }

    /**
     * @return Erga_Model_SubItems_Author
     */
    public function get_authorFromEmployee(Erga_Model_SubItems_SubProjectEmployee $employee) {
        foreach($this->get_authors() as $curAuthor) {
            if($curAuthor->get_employee() === $employee) {
                return $curAuthor;
            }
        }
        return null;
    }

    public function get_amount(Erga_Model_PersonnelCategories_PersonnelCategory $category = null) {
        if(!isset($category)) {
            return $this->_amount;
        } else {
            foreach($this->_limits as $curLimit) {
                if($curLimit->get_personnelcategory() === $category) {
                    return $curLimit->get_limit();
                }
            }
            return 0;
        }
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

    public function get_assignmentapprovaldate() {
        return $this->_assignmentapprovaldate;
    }

    public function set_assignmentapprovaldate($_assignapprovaldate) {
        $this->_assignmentapprovaldate = EDateTime::create($_assignapprovaldate);
    }

    public function get_completionapprovaldate() {
        return $this->_completionapprovaldate;
    }

    public function set_completionapprovaldate($_completionapprovaldate) {
        $this->_completionapprovaldate = EDateTime::create($_completionapprovaldate);
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function get_timesheetactivities() {
        return $this->_timesheetactivities;
    }

    public function set_timesheetactivities($_timesheetactivities) {
        $this->_timesheetactivities = $_timesheetactivities;
    }

    public function get_limits() {
        return $this->_limits;
    }

    public function set_limits($_limits) {
        $sum = 0;
        foreach($_limits as $curLimit) {
            $sum = $sum + $curLimit->get_limit();
        }
        if($sum > $this->get_amountAsFloat()) {
            throw new Exception('Το σύνολο των ορίων ανα κατηγορία προσωπικού του παραδοτέου ('.$sum.') ξεπερνά το ποσό του ('.$this->get_amountAsFloat().'). Οι αλλαγές δεν πραγματοποιήθηκαν.');
        }
        $this->_limits = $_limits;
    }

    public function get__duration() { // Το προεπιλεγμένο div θα χωρίσει το duration σε μήνες
        $days = $this->get_enddate()->diff($this->get_startdate())
                ->format("%a");
        return $days/30;
    }

    public function isComplete() {
        if(isset($this->_completionapprovaldate) && $this->_completionapprovaldate != "") {
            return true;
        } else {
            return false;
        }
    }

    public function isOverdue() {
        $curDate = new EDateTime();
        if(!isset($this->_completionapprovaldate) && $this->get_enddate() != null && $this->get_enddate() < $curDate) {
            return true;
        } else {
            return false;
        }
    }

    public function hasOverdueDeliverables() {
        return $this->isOverdue();
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubItems_WorkPackage) {
            $this->set_workpackage($owner);
        }
    }

    public function getEmployeePayment(Application_Model_Employee $employee) {
        $sum = 0;
        foreach($this->get_authors() as $curAuthor) {
            if($curAuthor->get_employee()->get_employee()->get_afm() === $employee->get_afm()) {
                $sum = $sum + $curAuthor->getPaidAmount();
            }
        }
        if($sum <= 0) {
            $sum = '-';
        }
        return $sum;
    }

    /**
     * @postPersist
     * @postUpdate
     */
    public function resetIsComplete() {
        // Reset the parent workpackage
        $workpackage = $this->get_workpackage();
        $workpackage->set_iscomplete(null);
        $workpackage->set_hasoverduedeliverables(null);
        Zend_Registry::get('entityManager')->persist($workpackage);
        // Reset the parent subproject
        $subproject = $workpackage->get_subproject();
        $subproject->set_iscomplete(null);
        $subproject->set_hasoverduedeliverables(null);
        Zend_Registry::get('entityManager')->persist($subproject);
        // Reset the parent project
        $project = $subproject->get_parentproject();
        $project->set_iscomplete(null);
        $project->set_hasoverduedeliverables(null);
        Zend_Registry::get('entityManager')->persist($project);
        Zend_Registry::get('entityManager')->flush();
    }

    public function __toString() {
        return $this->get_fulltitle();
    }
}
?>