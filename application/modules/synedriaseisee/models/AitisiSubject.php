<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Synedriaseisee_Model_Repositories_Subjects") @HasLifecycleCallbacks
 */
class Synedriaseisee_Model_AitisiSubject extends Synedriaseisee_Model_Subject {
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_AitisiBase", inversedBy="_subjects")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_AitisiBase
     */
    protected $_aitisi;

    public function get_aitisi() {
        // Για κάποιο λόγο χωρίς τις παρακάτω γραμμές η getOptionsAsStrings όταν γίνεται σε μια αίτηση πετάει το παρακάτω exception
        // A new entity was found through the relationship 'Synedriaseisee_Model_AitisiSubject#_aitisi' that was not configured to cascade persist operations
        if($this->_aitisi != null) {
            $this->_aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($this->_aitisi->get_aitisiid());
        }
        return $this->_aitisi;
    }

    public function set_aitisi(Aitiseis_Model_AitisiBase $_aitisi = null) {
        if($_aitisi != null) {
            // Προσθέτουμε τα session info στην αίτηση
            $_aitisi->set_session($this->get_synedriasi());
            $_aitisi->set_sessionsubject($this);
        } else {
            if(isset($this->_aitisi)) {
                // Διαγράφουμε τα session info από την αίτηση
                $this->_aitisi->set_session(null);
                $this->_aitisi->set_sessionsubject(null);
            }
        }
        $this->_aitisi = $_aitisi;
    }

    public function get_rawtitle() {
        if($this->get_aitisi() != null) {
            return $this->get_aitisi()->__toString();
        } else {
            return '';
        }
    }

    /** @PreRemove */
    public function onRemove() {
        $this->set_aitisi(null);
    }
}
?>