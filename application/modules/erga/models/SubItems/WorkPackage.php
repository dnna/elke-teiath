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

    protected $__iscomplete;

    protected $__hasoverduedeliverables;

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
        unset($this->__iscomplete);
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

    protected function get__iscomplete() {
        return $this->__iscomplete;
    }

    protected function set__iscomplete($__iscomplete) {
        $this->__iscomplete = $__iscomplete;
    }

    public function isComplete() {
        if(!isset($this->__iscomplete)) {
            if(!is_object($this->get_deliverables()) || $this->get_deliverables()->count() <= 0) {
                $this->__iscomplete = false;
                return false;
            }
            foreach($this->get_deliverables() as $curDeliverable) {
                if(!$curDeliverable->isComplete()) {
                    $this->__iscomplete = false;
                    return false;
                }
            }
            $this->__iscomplete = true;
            return true;
        }
        return $this->__iscomplete;
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

    protected function get__hasoverduedeliverables() {
        return $this->__hasoverduedeliverables;
    }

    protected function set__hasoverduedeliverables($__hasoverduedeliverables) {
        $this->__hasoverduedeliverables = $__hasoverduedeliverables;
    }

    public function hasOverdueDeliverables() {
        $overduedeliverables = Zend_Registry::get('entityManager')
            ->getRepository('Erga_Model_SubItems_Deliverable')
            ->findOverdueDeliverables();
        if(count($overduedeliverables) > 0) {
            foreach($overduedeliverables as $curOverdue) {
                if($curOverdue->get_workpackage()->get_recordid() === $this->get_recordid()) {
                    return true;
                }
            }
        }
        return false;
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