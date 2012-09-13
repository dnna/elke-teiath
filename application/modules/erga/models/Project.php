<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Erga_Model_Repositories_Projects") @Table(name="elke_erga.projects")
 */
class Erga_Model_Project extends Erga_Model_EmployeeContainer {
    // Βασικά στοιχεία έργου
    /**
     * @Id
     * @Column (name="projectid", type="integer")
     * @GeneratedValue
     */
    protected $_projectid;

    protected $_code = '--UNKNOWN_CODE--'; // Κωδικός έργου
    /**
     * @OneToOne (targetEntity="Erga_Model_ProjectBasicDetails", mappedBy="_project", cascade={"all"})
     * @JoinColumn (name="basicdetailsid", referencedColumnName="basicdetailsid")
     * @var Erga_Model_ProjectBasicDetails
     */
    protected $_basicdetails;

    // Συνεργαζόμενοι Φορείς
    /**
     * @OneToOne (targetEntity="Erga_Model_ProjectPosition", mappedBy="_project", cascade={"all"})
     * @JoinColumn (name="positionid", referencedColumnName="positionid")
     * @var Erga_Model_ProjectPosition
     */
    protected $_position;

    // Οικονομικά Στοιχεία
    /**
     * @OneToOne (targetEntity="Erga_Model_ProjectFinancialDetails", mappedBy="_project", cascade={"all"})
     * @JoinColumn (name="financialdetailsid", referencedColumnName="financialdetailsid")
     * @var Erga_Model_ProjectFinancialDetails
     */
    protected $_financialdetails;

    // Υποέργα
    /**
     * @Column (name="iscomplex", type="integer")
     */
    protected $_iscomplex = 1;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubProject", mappedBy="_parentproject", cascade={"all"})
     * @OrderBy ({"_subprojectnumber" = "ASC"})
     * @var ArrayCollection
     */
    protected $_subprojects;
    /**
     * @Column (name="subprojectsname", type="integer")
     */
    protected $_subprojectsname = 0;
    // Αιτήσεις
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_AitisiBase", mappedBy="_project")
     * @var Aitiseis_Model_AitisiBase
     */
    protected $_aitiseis;

    // Φύλλα Χρονοχρέωσης
    /**
     * @OneToMany (targetEntity="Timesheets_Model_Timesheet", mappedBy="_project", cascade={"all"})
     * @var ArrayCollection
     */
    protected $_timesheets;

    /**
     * @Column (name="creationdate", type="datetime")
     * @var EDateTime
     */
    protected $_creationdate;
    /**
     * @Column (name="lastupdatedate", type="datetime")
     * @var EDateTime
     */
    protected $_lastupdatedate;

    /**
     * @Column (name="iscomplete", type="integer")
     */
    protected $_iscomplete = false;
    /**
     * @Column (name="hasoverduedeliverables", type="integer")
     */
    protected $_hasoverduedeliverables = false;

    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_SubProjectEmployee", mappedBy="_project", cascade={"persist"})
     * @var Erga_Model_SubItems_SubProjectEmployee
     */
    protected $_employees; // Ονομαστική κατάσταση απασχολούμενων στο έργο
    protected $_thisprojectemployees; // Dummy var για την ανάκτηση του FullProject

    /**
     * @OneToMany (targetEntity="Erga_Model_PersonnelCategories_PersonnelCategory", mappedBy="_project", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_PersonnelCategories_PersonnelCategory
     */
    protected $_personnelcategories; // Ονομαστική κατάσταση απασχολούμενων στο έργο

    protected $_title;

    public function __construct(array $options = null) {
        parent::__construct($options);
        if(!isset($this->_creationdate)) {
            $this->_creationdate = new EDateTime("now");
        }

        // Δημιουργία των υποκατηγοριών
        $basicdetails = new Erga_Model_ProjectBasicDetails();
        $basicdetails->set_project($this);
        $this->set_basicdetails($basicdetails);

        $financialdetails = new Erga_Model_ProjectFinancialDetails();
        $financialdetails->set_project($this);
        $this->set_financialdetails($financialdetails);

        $position = new Erga_Model_ProjectPosition();
        $position->set_project($this);
        $this->set_position($position);
    }

    public function get_projectid() {
        return $this->_projectid;
    }

    public function set_projectid($_projectid) {
        $this->_projectid = $_projectid;
    }

    public function get_code() {
        if($this->get_basicdetails() != null) {
            if($this->get_basicdetails()->get_mis() != null) {
                $this->_code = 'MIS '.$this->get_basicdetails()->get_mis();
            } else {
                $this->_code = 'Κωδ. Λογ. '.$this->get_basicdetails()->get_acccode();
            }
        }
        return $this->_code;
    }

    public function get_creationdate() {
        return $this->_creationdate;
    }

    public function get_lastupdatedate() {
        if(isset($this->_lastupdatedate)) {
            return $this->_lastupdatedate;
        } else {
            return $this->get_creationdate();
        }
    }

    public function get_basicdetails() {
        if($this->_basicdetails != null) {
            return $this->_basicdetails;
        } else {
            $basicdetails = new Erga_Model_ProjectBasicDetails();
            $basicdetails->set_isvirtual(1);
            return $basicdetails;
        }
    }

    public function set_basicdetails($_basicdetails) {
        $this->_basicdetails = $_basicdetails;
    }

    public function get_position() {
        if($this->_position != null) {
            return $this->_position;
        } else {
            $position = new Erga_Model_ProjectPosition();
            $position->set_isvirtual(1);
            return $position;
        }
    }

    public function set_position($_position) {
        $this->_position = $_position;
    }

    public function get_financialdetails() {
        if($this->_financialdetails != null) {
            return $this->_financialdetails;
        } else {
            $financialdetails = new Erga_Model_ProjectFinancialDetails();
            $financialdetails->set_isvirtual(1);
            return $financialdetails;
        }
    }

    public function set_financialdetails($_financialdetails) {
        $this->_financialdetails = $_financialdetails;
    }

    public function get_iscomplex() {
        return $this->_iscomplex;
    }

    public function set_iscomplex($_iscomplex) {
        if($_iscomplex == 1 && $this->containsAitisiType('Aitiseis_Model_OnomastikiKatastasi')) {
            throw new Exception('Το έργο δεν μπορεί να μετατραπεί σε σύνθετο γιατί έχει συνδεδεμένη Ονομαστική Κατάσταση Απασχολούμενων.');
        }
        $iscomplexorig = $this->get_iscomplex();
        if($_iscomplex != $iscomplexorig) {
            if($_iscomplex == 0) {
                // Διαγράφουμε τα υποέργα και φτιάχνουμε ένα virtual υποέργο (isvirtual να είναι 1)
                $this->delete_subprojects(); // Διαγράφουμε τα παλιά υποέργα
                $this->createVirtualSubProject();
            } else if($_iscomplex == 1) {
                $this->getVirtualSubProject()->set_isvirtual(null); // Το υποέργο σταματάει να είναι virtual
            }
        }
        $this->_iscomplex = $_iscomplex;
    }

    public function containsAitisiType($type) {
        $aitiseis = $this->get_aitiseis();
        if($aitiseis == null || count($aitiseis) <= 0) {
            return false;
        }
        foreach($aitiseis as $curAitisi) {
            if(get_class($curAitisi) === $type) {
                return true;
            }
        }
        return false;
    }

    public function get_subprojects() {
        if(!isset($this->_subprojects)) {
            $this->_subprojects = new ArrayCollection();
        }
        return $this->_subprojects;
    }

    public function set_subprojects($_subprojects) {
        $this->_subprojects = $_subprojects;
    }

    public function getNextSubProjectNumber() {
        return ($this->get_subprojects()->count() + 1);
    }

    public function get_subprojectsname() {
        $names = self::getSubProjectNames();
        return $names[$this->_subprojectsname];
    }

    public function set_subprojectsname($_subprojectsname) {
        $this->_subprojectsname = $_subprojectsname;
    }

    public static function getSubProjectNames() {
        return array(
            0 => array('name' => 'Υποέργο', 'namepl' => 'Υποέργα', 'gen' => 'Υποέργου', 'genpl' => 'Υποέργων', 'new' => 'Νέου'),
            1 => array('name' => 'Δράση', 'namepl' => 'Δράσεις', 'gen' => 'Δράσης', 'genpl' => 'Δράσεων', 'new' => 'Νέας')
        );
    }

    protected function delete_subprojects() {
        if(isset($this->_subprojects)) {
            foreach($this->_subprojects as &$curSubproject) {
                Zend_Registry::get('entityManager')->remove($curSubproject);
            }
            $this->_subprojects->clear();
            Zend_Registry::get('entityManager')->flush();
        }
    }

    /**
     * Δημιουργεί ένα εικονικό υποέργο (χρησιμοποιείται για τα έργα που δεν
     * είναι σύνθετα).
     */
    protected function createVirtualSubProject() {
        $subproject = new Erga_Model_SubProject();
        $subproject->set_isvirtual(1);
        $subproject->set_subprojectdirectlabor(1);
        $subproject->set_parentproject($this);
        $subproject->createVirtualWorkPackage();
        $this->get_subprojects()->add($subproject);
        return $subproject;
    }

    /**
     * @return Erga_Model_SubProject
     */
    public function getVirtualSubProject() {
        if($this->get_subprojects()->count() > 0 && $this->get_subprojects()->first()->get_isvirtual() == 1) {
            return $this->get_subprojects()->first();
        } else if($this->get_subprojects()->first()->get_isvirtual() == 0) {
            throw new Exception('Σφάλμα στην ακεραιότητα των δεδομένων. Το πρώτο υποέργο δεν είναι virtual.');
        } else {
            return null;
        }
    }

    /**
     * Επιστρέφει μια συνολική εικόνα των απασχολούμενων στα υποέργα αυτού του
     * έργου. TODO Χρήση πιο efficient αλγορίθμου για το aggregation.
     */
    public function get_employees() {
        $employees = new ArrayCollection();
        if(isset($this->_employees)) {
            foreach($this->_employees as $curEmployee) {
                $employees->add($curEmployee);
            }
        }
        foreach($this->get_subprojects() as $curSubProject) {
            foreach($curSubProject->get_employees() as $curEmployee) {
                $employees->add($curEmployee);
            }
        }
        $iterator = $employees->getIterator();
        $iterator->natSort();
        return $iterator;
    }

    public function get_thisprojectemployees() {
        return $this->_employees;
    }

    public function set_employees($_employees) {
        $this->_employees = $_employees;
    }

    /**
     * Επιστρέφει μια συνολική εικόνα των αναδόχων στα υποέργα αυτού του
     * έργου. TODO Χρήση πιο efficient αλγορίθμου για το aggregation.
     */
    public function get_contractors() {
        $contractors = new ArrayCollection();
        foreach($this->get_subprojects() as $curSubProject) {
            foreach($curSubProject->get_contractors() as $curContractor) {
                $contractors->add($curContractor);
            }
        }
        return $contractors;
    }

    /**
     * Επιστρέφει ένα ArrayCollection με τους απασχολούμενους, αλλά κάθε
     * απασχολούμενος εμφανίζεται μόνο μια φορά και σαν αμοιβή εμφανίζεται το
     * σύνολο των αμοιβών για τα υποέργα στα οποία εργάζεται.
     */
    public function get_employeeTotals() {
        //return Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->findEmployeeTotals($this);
        throw new Exception('Not implemented');
    }
    
    public function get_personnelcategories() {
        return $this->_personnelcategories;
    }

    public function set_personnelcategories($_personnelcategories) {
        $this->_personnelcategories = $_personnelcategories;
    }

    public function get_personnelcategoriesAs2dArray() {
        $result = array();
        foreach($this->_personnelcategories as $curCategory) {
            $result[$curCategory->get_recordid()] = $curCategory->get_name();
        }
        return $result;
    }

    public function get_timesheets() {
        return $this->_timesheets;
    }

    public function set_timesheets($_timesheets) {
        $this->_timesheets = $_timesheets;
    }

    protected function get_iscomplete() {
        return $this->_iscomplete;
    }

    protected function set_iscomplete($_iscomplete) {
        $this->_iscomplete = $_iscomplete;
    }

    public function isComplete() {
        // ΔΕΝ σετάρουμε εδώ το _iscomplete γιατί χαλάει το lastupdatedate
        if(!is_object($this->get_subprojects()) || $this->get_subprojects()->count() <= 0) {
            $result = false;
        }
        foreach($this->get_subprojects() as $curSubproject) {
            if(!$curSubproject->isComplete()) {
                $result = false;
            }
        }
        if(!isset($result) || $result != false) {
            $result = true;
        }
        if($this->_iscomplete != $result) {
            $this->_iscomplete = $result;
        }
        return $result;
    }

    public function getCompletionDate() {
        if(!is_object($this->get_subprojects()) || $this->get_subprojects()->count() <= 0) {
            return null;
        }
        $completiondate = $this->get_subprojects()->get(0)->getCompletionDate();
        foreach($this->get_subprojects() as $curSubproject) {
            if($curSubproject->getCompletionDate() == null) {
                return null;
            } else if($curSubproject->getCompletionDate() > $completiondate) {
                $completiondate = $curSubproject->getCompletionDate();
            }
        }
        return $completiondate;
    }

    protected function get_hasoverduedeliverables() {
        return $this->_hasoverduedeliverables;
    }

    protected function set_hasoverduedeliverables($_hasoverduedeliverables) {
        $this->_hasoverduedeliverables = $_hasoverduedeliverables;
    }

    public function hasOverdueDeliverables() {
        if(!is_object($this->get_subprojects()) || $this->get_subprojects()->count() <= 0) {
            $this->_hasoverduedeliverables = false;
            return false;
        }
        foreach($this->get_subprojects() as $curSubproject) {
            if($curSubproject->hasOverdueDeliverables()) {
                $this->_hasoverduedeliverables = true;
                return true;
            }
        }
        $this->_hasoverduedeliverables = false;
        return false;
    }

    public function getTimesheetDeliverables(Erga_Model_SubItems_SubProjectEmployee $employee, EDateTime $month) {
        $deliverables = array();
        foreach($employee->get_isauthor() as $curAuthor) {
            // Επιλέγουμε το παραδοτέο όταν η ημερομηνία έναρξης είναι μέσα η πριν από το $month
            // και η ημερομηνία λήξης είναι μέσα η μετά από το $month
            $monthstart = clone $month;
            $monthend = clone $month;
            $monthend->add(date_interval_create_from_date_string(($monthstart->format('t') - 1).' days'));

            $deliverable = $curAuthor->get_deliverable();
            if($deliverable->get_startdate() <= $monthend && $deliverable->get_enddate() >= $monthstart) {
                $deliverables[] = $deliverable;
            }
        }
        natsort($deliverables);
        return $deliverables;
    }

    public function get_aitiseis() {
        return $this->_aitiseis;
    }

    public function set_aitiseis($_aitiseis) {
        $this->_aitiseis = $_aitiseis;
    }

    public function save() {
        if($this->get_iscomplex() == 0) {
            $subproject = $this->getVirtualSubProject();
            // Αν δεν υπάρχει η get_xyz() επιστρέφει ένα πλασματικό αντικείμενο
            $subproject->set_subprojecttitle($this->get_basicdetails()->get_title());
            $subproject->set_subprojecttitleen($this->get_basicdetails()->get_titleen());
            $subproject->set_subprojectsupervisor($this->get_basicdetails()->get_supervisor());
            $subproject->set_subprojectbudget($this->get_financialdetails()->get_budget());
            $subproject->set_subprojectstartdate($this->get_basicdetails()->get_startdate());
            $subproject->set_subprojectenddate($this->get_basicdetails()->get_enddate());
            $subproject->save();
        }
        return parent::save();
    }

    /**
     * Αποθηκεύει το project με στόχο να δημιουργηθούν τα κατάλληλα ids και να
     * αποφευχθούν τυχόν bugs. Αυτή η μέθοδος μόνη της ΔΕΝ εγγυάται την πλήρη
     * αποθήκευση του project και θα πρέπει να ακολουθείται από κλήση της
     * μεθόδου save().
     */
    public function simplySave() {
        $this->_lastupdatedate = new EDateTime("now");
        parent::save();
    }

    public function setOptions(array $options, $ignoreisvisible = false) {
        if(isset($options['default']) && count($options['default']) > 0) {
            $options = array_merge($options, $options['default']);
            unset($options['default']);
        }
        if(isset($options['aitiseis'])) {
            $em = Zend_Registry::get('entityManager');
            $aitiseis = $this->get_aitiseis();
            // Αφαιρούμε τα παλιά associations
            foreach($aitiseis as $curAitisi) {
                $aitiseis->removeElement($curAitisi);
                $curAitisi->set_project(null, false); // Null project με recursive false
            }
            $i = 1;
            while(isset($options['aitiseis'][$i])) {
                if($ignoreisvisible == true || (isset($options['aitiseis'][$i]['isvisible']) && $options['aitiseis'][$i]['isvisible'] === '1')) {
                    $aitisi = $em->getRepository('Aitiseis_Model_AitisiBase')->find($options['aitiseis'][$i]['aitisiid']);
                    if($aitisi != null) {
                        $aitisi->set_project($this);
                    } else {
                        throw new Exception('Κάποια από τις αιτήσεις δεν βρέθηκε.');
                    }
                }
                $i++;
            }
            unset($options['aitiseis']);
        }
        return parent::setOptions($options, $ignoreisvisible);
    }

    public function get_title() {
        if($this->get_basicdetails() != null && $this->get_basicdetails()->get_title() != null) {
            return $this->get_basicdetails()->get_title();
        } else {
            return "";
        }
        return $this->_title;
    }

    public function get_id() {
        return $this->get_projectid();
    }

    public function __toString() {
        return $this->get_title();
    }
}
?>