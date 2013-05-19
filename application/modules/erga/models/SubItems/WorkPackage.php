<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.workpackages")
 */
class Erga_Model_SubItems_WorkPackage extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubProject", inversedBy="_workpackages", cascade={"persist"})
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     */
    protected $_subproject; // Υποέργο
    /**
     * @Column (name="isvirtual", type="integer")
     */
    protected $_isvirtual;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_Deliverable", mappedBy="_workpackage", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_Deliverable
     */
    protected $_deliverables; // Παραδοτέα
    /** @Column (name="workpackagecodename", type="string") */
    protected $_workpackagecodename = "--NONAME--";
    /** @Column (name="workpackagename", type="string") */
    protected $_workpackagename = "--NONAME--";
    /**
     * @Column (name="iscomplete", type="integer")
     */
    protected $_iscomplete;
    /**
     * @Column (name="hasoverduedeliverables", type="integer")
     */
    protected $_hasoverduedeliverables;

    public function __construct($options = array()) {
        $this->_deliverables = new Doctrine\Common\Collections\ArrayCollection;
        parent::__construct($options);
    }

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject($_subproject) {
        $this->_subproject = $_subproject;
    }

    public function get_isvirtual() {
        return $this->_isvirtual;
    }

    public function set_isvirtual($_isvirtual) {
        $this->_isvirtual = $_isvirtual;
    }

    public function get_deliverables() {
        return $this->_deliverables;
    }

    /**
     * @return ArrayIterator
     */
    public function get_deliverablesNatsort() {
        $iterator = $this->_deliverables->getIterator();
        $iterator->natsort();
        return $iterator;
    }

    public function set_deliverables($_deliverables) {
        unset($this->_iscomplete);
        $this->_deliverables = $this->modifySubCollection($_deliverables, $this->_deliverables);
    }

    public function get_workpackagecodename() {
        return $this->_workpackagecodename;
    }

    public function set_workpackagecodename($_workpackagecodename) {
        $this->_workpackagecodename = $_workpackagecodename;
    }

    public function get_workpackagename() {
        return $this->_workpackagename;
    }

    public function set_workpackagename($_workpackagename) {
        $this->_workpackagename = $_workpackagename;
    }

    public function get_name() {
        return $this->get_workpackagecodename().' '.$this->get_workpackagename();
    }

    public function get_iscomplete() {
        return $this->_iscomplete;
    }

    public function set_iscomplete($_iscomplete) {
        $this->_iscomplete = $_iscomplete;
    }

    public function isComplete() {
        if(!isset($this->_iscomplete)) {
            if(!is_object($this->get_deliverables()) || $this->get_deliverables()->count() <= 0) {
                $this->_iscomplete = false;
                return false;
            }
            foreach($this->get_deliverables() as $curDeliverable) {
                if(!$curDeliverable->isComplete()) {
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
        if(!is_object($this->get_deliverables()) || $this->get_deliverables()->count() <= 0) {
            return null;
        }
        $completiondate = $this->get_deliverables()->get(0)->get_completionapprovaldate();
        foreach($this->get_deliverables() as $curDeliverable) {
            if($curDeliverable->get_completionapprovaldate() == null) {
                return null;
            } else if($curDeliverable->get_completionapprovaldate() > $completiondate) {
                $completiondate = $curDeliverable->get_completionapprovaldate();
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
            foreach($this->get_deliverables() as $curDeliverable) {
                if($curDeliverable->isOverdue()) {
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
    public function getDeliverableSumAmount(Erga_Model_PersonnelCategories_PersonnelCategory $category = null) {
        $sum = 0;
        foreach($this->get_deliverables() as $curDeliverable) {
            $sum = $sum + Zend_Locale_Format::getNumber($curDeliverable->get_amount($category), array('precision' => 2, 'locale' => Zend_Registry::get('Zend_Locale')));
        }
        return $sum;
    }

    public function getDeliverableSumAmountGreekFloat() {
        return Zend_Locale_Format::toNumber($this->getDeliverableSumAmount(),
                                        array(
                                              'precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale')));
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubProject) {
            $this->set_subproject($owner);
        }
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>