<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_SubProjects") @Table(name="elke_erga.subprojects")
 */
class Erga_Model_SubProject extends Erga_Model_EmployeeContainer {
    /**
     * @Id
     * @Column (name="subprojectid", type="integer")
     * @GeneratedValue
     */
    protected $_subprojectid;
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project", inversedBy="_subprojects", cascade={"persist"})
     * @JoinColumn (name="parentid", referencedColumnName="projectid")
     * @var Erga_Model_Project
     */
    protected $_parentproject;
    /**
     * @Column (name="isvirtual", type="integer")
     */
    protected $_isvirtual;
    /**
     * @Column (name="number", type="integer")
     * @FormFieldLabel Αριθμός Υποέργου
     */
    protected $_subprojectnumber = 1;
    /**
     * @Column (name="title", type="string")
     * @FormFieldLabel Τίτλος
     */
    protected $_subprojecttitle = "--NONAME--";
    /**
     * @Column (name="titleen", type="string")
     * @FormFieldLabel Τίτλος (στα Αγγλικά)
     */
    protected $_subprojecttitleen; // Ο τίτλος στα Αγγλικά
    /**
     * @ManyToOne (targetEntity="Application_Model_User", cascade={"persist"})
     * @JoinColumn (name="supervisoruserid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_subprojectsupervisor;
    /**
     * @Column (name="description", type="string")
     */
    protected $_subprojectdescription;
    /**
     * @Column (name="budget", type="greekfloat")
     */
    protected $_subprojectbudget;
    /**
     * @Column (name="budgetfpa", type="greekfloat")
     */
    protected $_subprojectbudgetfpa;
    /**
     * @Column (name="startdate", type="date")
     * @FormFieldLabel Ημερομηνία Έναρξης
     * @var EDateTime
     */
    protected $_subprojectstartdate;
    /**
     * @Column (name="enddate", type="date")
     * @FormFieldLabel Ημερομηνία Λήξης
     * @var EDateTime
     */
    protected $_subprojectenddate;
    /**
     * @Column (name="type", type="integer")
     */
    protected $_subprojecttype;
    const TYPE_MELETI = 0;
    const TYPE_PROION = 1;
    const TYPE_YPHRESIA = 2;
    /**
     * @Column (name="directlabor", type="integer")
     */
    protected $_subprojectdirectlabor; // Αυτεπιστασία
    /**
     * @OneToMany (targetEntity="Praktika_Model_Competition", mappedBy="_subproject", cascade={"all"})
     * @var Praktika_Model_Competition
     */
    protected $_competition; // Αν είναι null τότε το έργο είναι αυτεπιστασία

    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_SubProjectEmployee", mappedBy="_subproject", cascade={"persist"})
     * @var Erga_Model_SubItems_SubProjectEmployee
     */
    protected $_employees; // Ονομαστική κατάσταση απασχολούμενων στο έργο
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_SubProjectContractor", mappedBy="_subproject", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_SubProjectContractor
     */
    protected $_contractors; // Ανάδοχοι
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_WorkPackage", mappedBy="_subproject", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_WorkPackage
     */
    protected $_workpackages; // Πακέτα Εργασίας
    /**
     * @Column (name="comments", type="string")
     */
    protected $_comments; // Γενικές Παρατηρήσεις
    /**
     * @Column (name="iscomplete", type="integer")
     */
    protected $_iscomplete;
    /**
     * @Column (name="hasoverduedeliverables", type="integer")
     */
    protected $_hasoverduedeliverables;

    /**
     * @return Application_Model_ProjectBase
     */
    public function get_parentproject() {
        return $this->_parentproject;
    }

    public function set_parentproject($_parentproject) {
        $this->_parentproject = $_parentproject;
    }

    public function get_isvirtual() {
        return $this->_isvirtual;
    }

    public function set_isvirtual($_isvirtual) {
        $this->_isvirtual = $_isvirtual;
    }

    public function get_subprojectid() {
        return $this->_subprojectid;
    }

    public function set_subprojectid($_subprojectid) {
        $this->_subprojectid = $_subprojectid;
    }

    public function get_subprojectnumber() {
        return $this->_subprojectnumber;
    }

    public function set_subprojectnumber($_subprojectnumber) {
        $this->_subprojectnumber = $_subprojectnumber;
    }

    public function get_subprojecttitle() {
        return $this->_subprojecttitle;
    }

    public function set_subprojecttitle($_subprojecttitle) {
        $this->_subprojecttitle = $_subprojecttitle;
    }

    public function get_subprojecttitleen() {
        return $this->_subprojecttitleen;
    }

    public function set_subprojecttitleen($_subprojecttitleen) {
        $this->_subprojecttitleen = $_subprojecttitleen;
    }

    public function get_subprojectsupervisor() {
        return $this->_subprojectsupervisor;
    }

    public function set_subprojectsupervisor($_subprojectsupervisor) {
        $this->_subprojectsupervisor = $_subprojectsupervisor;
    }

    public function get_subprojectdescription() {
        return $this->_subprojectdescription;
    }

    public function set_subprojectdescription($_subprojectdescription) {
        $this->_subprojectdescription = $_subprojectdescription;
    }

    public function get_subprojectbudget() {
        return $this->_subprojectbudget;
    }

    public function set_subprojectbudget($_subprojectbudget) {
        $this->_subprojectbudget = $_subprojectbudget;
    }

    public function get_subprojectbudgetfpa() {
        return $this->_subprojectbudgetfpa;
    }

    public function set_subprojectbudgetfpa($_subprojectbudgetfpa) {
        $this->_subprojectbudgetfpa = $_subprojectbudgetfpa;
    }

    public function get_subprojectbudgetwithfpa() {
        if($this->get_subprojectbudget() != null && $this->get_subprojectbudgetfpa() != null) {
            $budget = Zend_Locale_Format::getNumber($this->get_subprojectbudget(),
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       );
            $budgetfpa = Zend_Locale_Format::getNumber($this->get_subprojectbudgetfpa(),
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       );
            return Zend_Locale_Format::toNumber($budget + $budgetfpa,
                                        array(
                                              'precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale')));
        } else if($this->get_subprojectbudget() != null) {
            return $this->get_subprojectbudget();
        } else {
            return null;
        }
    }

    public function get_subprojectstartdate() {
        return $this->_subprojectstartdate;
    }

    public function set_subprojectstartdate($_startdate) {
        $this->_subprojectstartdate = EDateTime::create($_startdate);
    }

    public function get_subprojectenddate() {
        return $this->_subprojectenddate;
    }

    public function set_subprojectenddate($_enddate) {
        $this->_subprojectenddate = EDateTime::create($_enddate);
    }

    public function get_subprojecttype() {
        return $this->_subprojecttype;
    }

    public function set_subprojecttype($_subprojecttype) {
        $this->_subprojecttype = $_subprojecttype;
    }

    /**
     * Επιστρέφει αν το υποέργο είναι αυτεπιστασία ή διαγωνισμός.
     * @return boolean true αν είναι αυτεπιστασία, false αν είναι διαγωνισμός.
     */
    public function get_subprojectdirectlabor() {
        return $this->_subprojectdirectlabor;
    }

    public function set_subprojectdirectlabor($_subprojectdirectlabor) {
        $this->_subprojectdirectlabor = $_subprojectdirectlabor;
        if($_subprojectdirectlabor == 1) {
            $this->set_competition(null);
        }
        $this->updateEmployeesAndContractors();
    }

    public function get_competition() {
        if(isset($this->_competition)) {
            if($this->get_subprojectdirectlabor() != 1 && $this->_competition->get(0) == null) {
                $competition = $this->newCompetition();
                Zend_Registry::get('entityManager')->persist($competition);
                Zend_Registry::get('entityManager')->flush(); // Για να έχει id
                $this->set_competition($competition);
            }
        } else {
            $competition = $this->newCompetition();
            //$this->set_competition($competition);
        }
        return $this->_competition->get(0);
    }

    private function newCompetition() {
        $competition = new Praktika_Model_Competition();
        $competition->set_subproject($this);
        return $competition;

    }

    public function set_competition($_competition) {
        if(!is_object($this->_competition)) {
            $this->_competition = new ArrayCollection();
        }
        $oldcompetition = $this->_competition->get(0);
        if($_competition == null) {
            if($oldcompetition != null) {
                //Zend_Registry::get('entityManager')->remove($oldcompetition);
                // Αφαιρούμε το subproject από τον διαγωνισμό. Αν είναι ορφανό θα καθαριστεί αργότερα από το garbagecollection
                $this->_competition->removeElement($oldcompetition);
                $oldcompetition->set_subproject(null);
            }
        } else {
            $this->_competition->set(0, $_competition);
        }
    }

    /**
     * Αν το υποέργο είναι αυτεπιστασία, διαγράφει τυχόν αναδόχους που μπορεί να
     * έχουν περαστεί, ενώ αν είναι διαγωνισμός καθαρίζει τη λίστα των
     * απασχολούμενων. Αυτές οι κινήσεις ενημέρωνουν αυτόματα και τα παρααδοτέα.
     */
    protected function updateEmployeesAndContractors() {
        $em = Zend_Registry::get('entityManager');
        if($this->get_subprojectdirectlabor() == "1" && $this->get_contractors() != null && $this->get_contractors()->count() > 0) {
            foreach($this->get_contractors() as $curContractor) {
                $em->remove($curContractor);
                $this->get_contractors()->removeElement($curContractor);
            }
            //throw new Exception('Το υποέργο δεν μπόρεσε να αλλάξει σε αυτεπιστασία γιατί περιέχει αναδόχους.');
        } else if($this->get_subprojectdirectlabor() == "0" && $this->get_employees() != null && $this->get_employees()->count() > 0) {
            foreach($this->get_employees() as $curEmployee) {
                $em->remove($curEmployee);
                $this->get_employees()->removeElement($curEmployee);
            }
            //throw new Exception('Το υποέργο δεν μπόρεσε να αλλάξει σε διαγωνισμό γιατί περιέχει απασχολούμενους.');
        }
    }

    public function get_employees() {
        if(!isset($this->_employees)) {
            return array();
        }
        $employees = $this->_employees->toArray();
        usort($employees, array("Erga_Model_SubItems_SubProjectEmployee", "compareEmployees"));
		// Increase index to fix a Dnna_Form_Base bug
        array_unshift($employees, null);
        unset($employees[0]);
		// End bug fix
        return $employees;
    }

    public function set_employees($_employees) {
        $this->_employees = $_employees;
    }

    public function get_contractors() {
        return $this->_contractors;
    }

    public function set_contractors($_contractors) {
        $this->_contractors = $_contractors;
    }

    public function get_workpackages() {
        if(!isset($this->_workpackages)) {
            $this->_workpackages = new ArrayCollection();
        }
        return $this->_workpackages;
    }

    /**
     * @return ArrayIterator
     */
    public function get_workpackagesNatsort() {
        $iterator = $this->_workpackages->getIterator();
        $iterator->natsort();
        return $iterator;
    }

    public function set_workpackages($_workpackages) {
        $this->_workpackages = $_workpackages;
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    /**
     * Δημιουργεί ένα εικονικό πακέτο εργασίας (χρησιμοποιείται για τα έργα που
     * δεν είναι σύνθετα).
     */
    public function createVirtualWorkPackage() {
        $workPackage = new Erga_Model_SubItems_WorkPackage();
        $workPackage->set_isvirtual(1);
        $workPackage->set_subproject($this);
        $this->get_workpackages()->add($workPackage);
        return $workPackage;
    }

    public function getVirtualWorkPackage() {
        if($this->get_workpackages()->count() > 0 && $this->get_workpackages()->first()->get_isvirtual() == 1) {
            return $this->get_workpackages()->first();
        } else {
            return null;
        }
    }

    public function get_iscomplete() {
        return $this->_iscomplete;
    }

    public function set_iscomplete($_iscomplete) {
        $this->_iscomplete = $_iscomplete;
    }

    public function isComplete() {
        if(!isset($this->_iscomplete)) {
            if(!is_object($this->get_workpackages()) || $this->get_workpackages()->count() <= 0) {
                $this->_iscomplete = false;
                return false;
            }
            foreach($this->get_workpackages() as $curWorkpackage) {
                if(!$curWorkpackage->isComplete()) {
                    $this->_iscomplete = false;
                    return false;
                }
            }
            $this->_iscomplete = true;
            return true;
        }
        return $this->_iscomplete;
    }

    public function getCompletionDate() {
        if(!is_object($this->get_workpackages()) || $this->get_workpackages()->count() <= 0) {
            return null;
        }
        $completiondate = $this->get_workpackages()->get(0)->getCompletionDate();
        foreach($this->get_workpackages() as $curWorkpackage) {
            if($curWorkpackage->getCompletionDate() == null) {
                return null;
            } else if($curWorkpackage->getCompletionDate() > $completiondate) {
                $completiondate = $curWorkpackage->getCompletionDate();
            }
        }
        return $completiondate;
    }

    public function get_hasoverduedeliverables() {
        return $this->_hasoverduedeliverables;
    }

    public function set_hasoverduedeliverables($_hasoverduedeliverables) {
        $this->_hasoverduedeliverables = $_hasoverduedeliverables;
    }

    public function hasOverdueDeliverables() {
        if(!isset($this->_hasoverduedeliverables)) {
            if(!is_object($this->get_workpackages()) || $this->get_workpackages()->count() <= 0) {
                $this->_hasoverduedeliverables = false;
                return false;
            }
            foreach($this->get_workpackages() as $curWorkpackage) {
                if($curWorkpackage->hasOverdueDeliverables()) {
                    $this->_hasoverduedeliverables = true;
                    return true;
                }
            }
            $this->_hasoverduedeliverables = false;
            return false;
        }
        return $this->_hasoverduedeliverables;
    }

    // Επιστρέφει Αμερικάνικο float
    public function getWorkpackagesSumAmount() {
        $sum = 0;
        foreach($this->get_workpackages() as $curWorkpackage) {
            $sum = $sum + $curWorkpackage->getDeliverableSumAmount();
        }
        return $sum;
    }

    public function getWorkpackagesSumAmountGreekFloat() {
        return Zend_Locale_Format::toNumber($this->getWorkpackagesSumAmount(),
                                        array(
                                              'precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale')));
    }

    public function save() {
        if($this->get_parentproject()->get_iscomplex() == 0) {
            $workpackage = $this->getVirtualWorkPackage();
            $workpackage->set_workpackagecodename('');
            $workpackage->set_workpackagename($this->get_subprojecttitle());
            $workpackage->save();
        }
        return parent::save();
    }

    public function __toString() {
        return $this->get_subprojecttitle();
    }
}
?>