<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Agencies") @Table (name="agencies")
 */
class Application_Model_Lists_Agency extends Dnna_Model_Object implements Application_Model_Lists_ListInterface {
    /**
     * @Id
     * @Column (name="id", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_id;
    /**
     * @Column (name="name", type="string")
     * @FormFieldLabel Επωνυμία
     * @FormFieldRequired
     * @IsKeyElement
     */
    protected $_name;
    /**
     * @Column (name="address", type="string")
     * @FormFieldLabel Διεύθυνση
     */
    protected $_address;
    /**
     * @Column (name="afm", type="string")
     * @FormFieldLabel ΑΦΜ
     */
    protected $_afm;
    /**
     * @Column (name="doy", type="string")
     * @FormFieldLabel ΔΟΥ
     */
    protected $_doy;
    /**
     * @Column (name="type", type="string")
     * @FormFieldLabel Είδος Φορέα
     * @FormFieldType SimpleSelect
     * @FormFieldOptions Δημόσιος, Ιδιωτικός, Ευρωπαϊκό Πρόγραμμα, Ίδιοι Πόροι, Αυτοχρηματοδότηση
     * @FormFieldRequired
     */
    protected $_type;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        if($_name != "") {
            $this->_name = $_name;
        } else {
            throw new Exception('Η επωνυμία του αναδόχου δεν μπορεί να είναι κενή.');
        }
    }

    public function get_address() {
        return $this->_address;
    }

    public function set_address($_address) {
        $this->_address = $_address;
    }

    public function get_afm() {
        return $this->_afm;
    }

    public function set_afm($_afm) {
        $this->_afm = $_afm;
    }

    public function get_doy() {
        return $this->_doy;
    }

    public function set_doy($_doy) {
        $this->_doy = $_doy;
    }

    public function get_type() {
        return $this->_type;
    }

    public function get_typeAsString() {
        $reflection = new Zend_Reflection_Class(get_class($this));
        $property = $reflection->getProperty('_type');
        $docblock = $property->getDocComment();
        $options = explode(', ', $docblock->getTag('FormFieldOptions')->getDescription());
        return $options[$this->get_type()];
    }

    public function set_type($_type) {
        $this->_type = $_type;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>