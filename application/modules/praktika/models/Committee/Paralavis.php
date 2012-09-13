<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Praktika") @HasLifecycleCallbacks
 */
class Praktika_Model_Committee_Paralavis extends Praktika_Model_CommitteeBase {
    const type = "Επιτροπή Παραλαβής";
    const formclass = "Praktika_Form_Committee_Paralavis";
    /**
     * @OneToOne (targetEntity="Aitiseis_Model_DhmiourgiaEpitropisParalavis", inversedBy="_receiptcommittee")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_DhmiourgiaEpitropisParalavis
     */
    protected $_aitisi;
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     * @var Erga_Model_Project
     */
    protected $_project;

    /**
     * @postPersist
     * @postUpdate
     */
    public function postPersist() {
        if($this->activechanged && $this->_active == true) {
            //$emailcommittee = Zend_Controller_Action_HelperBroker::getStaticHelper('EmailCommittee');
            //$emailcommittee->direct($this, 'informepitropiparalavis');
        }
    }

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }
}
?>