<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Lists") @Table(name="expenditurecategories")
 */
class Application_Model_Lists_ExpenditureCategory extends Dnna_Model_Object implements Application_Model_Lists_ListInterface {
    /**
     * @Id
     * @Column (name="id", type="integer")
     * @GeneratedValue (strategy="AUTO")
     */
    protected $_id;
    /**
     * @Column (name="name", type="string")
     * @FormFieldLabel Ονομασία Δαπάνης
     * @FormFieldRequired
     */
    protected $_name;

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
        $this->_name = $_name;
    }

    public function __toString() {
        return $this->get_name();
    }
}
?>