<?php

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.authors")
 */
class Erga_Model_SubItems_Author extends Application_Model_SubObject {

    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_Deliverable", inversedBy="_authors", cascade={"persist"})
     * @JoinColumn (name="deliverableid", referencedColumnName="recordid")
     */
    protected $_deliverable; // Παραδοτέο
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectEmployee", inversedBy="_isauthor")
     * @JoinColumn (name="authorid", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_SubProjectEmployee
     */
    protected $_employee;

    /**
     * @ManyToOne (targetEntity="Erga_Model_PersonnelCategories_PersonnelCategory")
     * @JoinColumn (name="personnelcategoryid", referencedColumnName="recordid")
     * @var Erga_Model_PersonnelCategories_PersonnelCategory
     */
    protected $_personnelcategory;

    /** @Column (name="rate", type="greekfloat") */
    protected $_rate; // Ωρομίσθιο
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount; // Συνολικό ποσό

    public function get_deliverable() {
        return $this->_deliverable;
    }

    public function set_deliverable($_deliverable) {
        $this->_deliverable = $_deliverable;
    }

    public function get_employee() {
        return $this->_employee;
    }

    public function set_employee($_employee) {
        $this->_employee = $_employee;
    }

    public function get_personnelcategory() {
        return $this->_personnelcategory;
    }

    public function set_personnelcategory($_personnelcategory) {
        $this->_personnelcategory = $_personnelcategory;
    }

    public function get_rate() {
        return $this->_rate;
    }

    public function set_rate($_rate) {
        $this->_rate = $_rate;
    }

    public function get_rateAsFloat() {
        return Zend_Locale_Format::getNumber($this->get_rate(), array('precision' => 2,
                    'locale' => Zend_Registry::get('Zend_Locale'))
        );
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }

    public function get_amountAsFloat() {
        if ($this->get_amount() != '') {
            return Zend_Locale_Format::getNumber($this->get_amount(), array('precision' => 2,
                        'locale' => Zend_Registry::get('Zend_Locale'))
            );
        } else {
            return 0;
        }
    }

    public function get_rateOrAmount() {
        if (isset($this->_amount) && $this->_amount > 0) {
            return ' (' . $this->_amount . '&euro;)';
        } else if (isset($this->_rate) && $this->_rate > 0) {
            return '&nbsp;(' . $this->_rate . '&euro;/ώρα)';
        }
    }

    public function get_workedhours() {
        $sum = 0;
        foreach ($this->_deliverable->get_timesheetactivities() as $curActivity) {
            if ($curActivity->get_timesheet()->get_employee() === $this->_employee) {
                $sum = $sum + $curActivity->get_duration();
            }
        }
        return $sum;
    }

    public function getPaidAmount() {
        if (isset($this->_amount) && $this->_amount > 0) {
			return Zend_Locale_Format::getNumber($this->_amount, array('precision' => 2,
						'locale' => Zend_Registry::get('Zend_Locale'))
			);
        } else {
            $timesheetpaid = ($this->get_workedhours() * $this->get_rateAsFloat());
            if ($timesheetpaid > 0) {
                return $timesheetpaid;
            } else {
                $curDeliverable = $this->get_deliverable();
                if ($curDeliverable->get_authors()->count() <= 1 && $curDeliverable->get_authors()->get(0)->get_rate() == null) {
                    return Zend_Locale_Format::getNumber($curDeliverable->get_amount(), array('precision' => 2,
                                'locale' => Zend_Registry::get('Zend_Locale'))
                    );
                } else {
                    return 0;
                }
            }
        }
    }

    public function setOwner($owner) {
        if ($owner == null || $owner instanceof Erga_Model_SubItems_Deliverable) {
            $this->set_deliverable($owner);
        }
    }

    public function __toString() {
        return $this->get_employee()->get_employee()->get_name();
    }

}

?>