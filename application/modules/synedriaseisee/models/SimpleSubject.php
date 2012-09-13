<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Synedriaseisee_Model_Repositories_Subjects")
 */
class Synedriaseisee_Model_SimpleSubject extends Synedriaseisee_Model_Subject {
    /**
     * @Column (name="title", type="string")
     */
    protected $_title; // Ένα θέμα μπορεί είτε να έχει τίτλο είτε να αφορά μια αίτηση

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
    }

    public function get_rawtitle() {
        return $this->get_title();
    }
}
?>