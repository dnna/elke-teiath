<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.timesheet_activities")
 */
class Timesheets_Model_Activity extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Timesheets_Model_Timesheet", inversedBy="_timesheets", cascade={"persist"})
     * @JoinColumn (name="timesheetid", referencedColumnName="id")
     * @var Timesheets_Model_Timesheet
     */
    protected $_timesheet;
    /**
     * @Column (name="day", type="integer")
     */
    protected $_day;
    /**
     * @Column (name="start", type="time")
     */
    protected $_start;
    /**
     * @Column (name="end", type="time")
     */
    protected $_end;
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_Deliverable", inversedBy="_timesheetactivities")
     * @JoinColumn (name="deliverableid", referencedColumnName="recordid")
     * @var Erga_Model_SubItems_Deliverable
     */
    protected $_deliverable;

    public function get_timesheet() {
        return $this->_timesheet;
    }

    public function set_timesheet($_timesheet) {
        $this->_timesheet = $_timesheet;
    }

    public function get_day() {
        return $this->_day;
    }

    public function set_day($_day) {
        $this->_day = $_day;
    }

    public function get_start() {
        if(isset($this->_start)) {
            return $this->_start->format('G');
        } else {
            return null;
        }
    }

    public function get_startAsDate() {
        return $this->_start;
    }

    public function set_start($_start) {
        if(strpos($_start, ':') !== false) {
            $this->_start = new DateTime($_start);
        } else {
            $this->_start = new DateTime($_start.':00');
        }
    }

    public function get_end() {
        if(isset($this->_end)) {
            return $this->_end->format('G');
        } else {
            return null;
        }
    }

    public function get_endAsDate() {
        return $this->_end;
    }

    public function set_end($_end) {
        if(strpos($_end, ':') !== false) {
            $this->_end = new DateTime($_end);
        } else {
            $this->_end = new DateTime($_end.':00');
        }
    }

    public function get_deliverable() {
        return $this->_deliverable;
    }

    public function set_deliverable(Erga_Model_SubItems_Deliverable $_deliverable) {
        $this->_deliverable = $_deliverable;
    }

    public function get_duration() {
        return ($this->_end - $this->_start);
    }

    public function getHours() {
        return round(($this->_end->getTimestamp() - $this->_start->getTimestamp())/3600, 2);
    }

    public function get_date() {
        $year = $this->_timesheet->get_year();
        $month = $this->_timesheet->get_month();
        $day = $this->get_day();
        return \EDateTime::createFromFormat('Y-m-d', $year.'-'.$month.'-'.$day);
    }

    public function setOwner($owner) {
        if($owner instanceof Timesheets_Model_Timesheet) {
            $this->set_timesheet($owner);
        }
    }
}
?>