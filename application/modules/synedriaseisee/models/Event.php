<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @MappedSuperclass
 */
abstract class Synedriaseisee_Model_Event extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="id", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_id;
    /**
     * @Column (name="start", type="datetime")
     * @var EDateTime
     */
    protected $_start;
    /**
     * @Column (name="end", type="datetime")
     * @var EDateTime
     */
    protected $_end;

    protected $_title;

    protected $_allDay = false;

    protected $_cssClass = 'default';

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_start() {
        return $this->_start;
    }

    public function set_start($_start) {
        $this->_start = EDateTime::create($_start);
    }

    public function get_end() {
        return $this->_end;
    }

    public function set_end($_end) {
        $this->_end = EDateTime::create($_end);
    }

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
    }

    public function get_allDay() {
        return $this->_allDay;
    }

    public function set_allDay($_allDay) {
        $this->_allDay = $_allDay;
    }

    public function get_cssClass() {
        return $this->_cssClass;
    }

    public function set_cssClass($_cssClass) {
        $this->_cssClass = $_cssClass;
    }

    public function __toString() {
        return $this->get_title();
    }
}
?>