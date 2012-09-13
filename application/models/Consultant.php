<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke.consultants")
 */
class Application_Model_Consultant extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="id", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_id;
    /**
     * @Column (name="name", type="string")
     * @IsKeyElement
     * @FormFieldLabel Όνομα
     */
    protected $_name;
    /**
     * @Column (name="capacity", type="string")
     * @FormFieldLabel Ιδιότητα
     */
    protected $_capacity;
    /**
     * @Column (name="phone", type="string")
     * @FormFieldLabel Τηλέφωνο
     */
    protected $_phone;
    /**
     * @OneToOne (targetEntity="Praktika_Model_Competition", mappedBy="_technicalconsultant")
     */
    protected $_astechnicalconsultant;
    /**
     * @OneToOne (targetEntity="Praktika_Model_Competition", mappedBy="_responsibleperson")
     */
    protected $_asresponsibleperson;

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
    }

    public function get_capacity() {
        return $this->_capacity;
    }

    public function set_capacity($_capacity) {
        $this->_capacity = $_capacity;
    }

    public function get_phone() {
        return $this->_phone;
    }

    public function set_phone($_phone) {
        $this->_phone = $_phone;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>