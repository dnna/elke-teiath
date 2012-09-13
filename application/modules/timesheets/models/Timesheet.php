<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Timesheets_Model_Repositories_Timesheets") @Table(name="elke_erga.timesheets") @HasLifecycleCallbacks
 */
class Timesheets_Model_Timesheet extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="id", type="string")
     */
    protected $_id;
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectEmployee", inversedBy="_timesheets")
     * @JoinColumn (name="employeeid", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_SubProjectEmployee
     */
    protected $_employee;
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project", inversedBy="_timesheets", cascade={"persist"})
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     * @var Erga_Model_Project
     */
    protected $_project;
    /**
     * @Column (name="month", type="integer")
     */
    protected $_month;
    /**
     * @Column (name="year", type="integer")
     */
    protected $_year;
    /**
     * @OneToMany (targetEntity="Timesheets_Model_Activity", mappedBy="_timesheet", cascade={"all"})
     * @var Timesheets_Model_Activity
     */
    protected $_activities;

    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    /**
     * @Column (name="approved", type="integer")
     */
    protected $_approved = self::PENDING;
    protected $_approvedtext;

    public function __construct(array $options = null) {
        $this->_activities = new ArrayCollection();
        parent::__construct($options);
    }

    /** @PrePersist */
    public function generateId()
    {
        $this->_id = $this->get_project()->get_projectid().'-'.$this->get_employee()->get_employee()->get_afm().'-'.$this->get_employee()->get_recordid().'-'.$this->get_month().'-'.$this->get_year();
        return $this->_id;
    }

    public function get_id() {
        if(!isset($this->_id)) {
            $this->generateId();
        }
        return $this->_id;
    }

    public function get_employee() {
        return $this->_employee;
    }

    public function set_employee($_employee) {
        $this->_employee = $_employee;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }

    public function get_month() {
        return $this->_month;
    }

    public function set_month($_month) {
        $this->_month = $_month;
    }

    public function get_monthAsDate() {
        return new EDateTime($this->get_year().'-'.$this->get_month().'-1');
    }

    public function get_year() {
        return $this->_year;
    }

    public function set_year($_year) {
        $this->_year = $_year;
    }

    public function get_activities() {
        return $this->_activities;
    }

    public function set_activities($_activities) {
        $this->_activities = $_activities;
    }
    
    public function get_activitiesForDeliverable(Erga_Model_SubItems_Deliverable $deliverable) {
        $activities = array();
        foreach($this->get_activities() as $curActivity) {
            if($curActivity->get_deliverable() === $deliverable) {
                $activities[] = $curActivity;
            }
        }
        return $activities;
    }

    public function get_activitiesForDay($day) {
        $activities = array();
        foreach($this->get_activities() as $curActivity) {
            if($curActivity->get_day() === $day) {
                $activities[] = $curActivity;
            }
        }
        return $activities;
    }

    public function get_approved() {
        return $this->_approved;
    }

    public function set_approved($_approved) {
        if($_approved == self::APPROVED) {
            require_once(APPLICATION_PATH . '/modules/timesheets/controllers/helpers/CheckApprovalValidity.php');
            $validator = new Timesheets_Action_Helper_CheckApprovalValidity();
            $validator->direct($this);
        }
        $this->_approved = $_approved;
    }

    public function get_approvedtext() {
        if($this->_approved == self::APPROVED) {
            $this->_approvedtext = 'Εγκρίθηκε';
        } else if($this->_approved == self::REJECTED) {
            $this->_approvedtext = 'Απορρίφθηκε';
        } else {
            $this->_approvedtext = 'Εκκρεμεί';
        }
        return $this->_approvedtext;
    }

    /**
     * Επιστρέφει το σύνολο των ωρών του απασχολούμενου από το συγκεκριμένο
     * φύλλο.
     * @return int
     */
    public function getTotalHours() {
        $sum = 0;
        foreach($this->_activities as $curActivity) {
            $sum = $sum + $curActivity->getHours();
        }
        return $sum;
    }

    public function getPaidAmount() {
        $sum = 0;
        foreach($this->_activities as $curActivity) {
            $rate = $curActivity->get_deliverable()->get_authorFromEmployee($this->get_employee())->get_rateAsFloat();
            if(isset($rate)) {
                $sum = $sum + $curActivity->getHours()*$rate;
            }
        }
        if($sum == 0) {
            $amount = $curActivity->get_deliverable()->get_authorFromEmployee($this->get_employee())->get_amount();
            if(isset($amount)) {
                $sum = $sum + $amount;
            }
        }
        return $sum;
    }

    public function __toString() {
        return 'Φύλλο Χρονοχρέωσης Έργου: '.$this->get_project()->get_code().' για εργαζόμενο με ΑΦΜ: '.$this->get_employee()->get_employee()->get_afm();
    }
}
?>