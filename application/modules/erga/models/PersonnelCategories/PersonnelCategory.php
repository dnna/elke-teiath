<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.personnelcategories")
 */
class Erga_Model_PersonnelCategories_PersonnelCategory extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project", inversedBy="_personnelcategories")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     */
    protected $_project;
    /** @Column (name="name", type="string") */
    protected $_name;

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_Project) {
            $this->set_project($owner);
        }
    }

    public function __toString() {
        return $this->get_name();
    }
}

?>