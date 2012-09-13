<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.dhmiorgiaepitropisparalavis")
 */
class Aitiseis_Model_DhmiourgiaEpitropisParalavis extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση για Ορισμό Επιτροπής Παραλαβής";
    const formclass = "Aitiseis_Form_DhmiourgiaEpitropisParalavis";
    const template = "AitisiOrismouEpitropisParalavis";

    /**
     * @OneToOne (targetEntity="Praktika_Model_Committee_Paralavis", cascade={"persist"}, mappedBy="_aitisi")
     * @var Praktika_Model_Committee_Paralavis
     */
    //protected $_receiptcommittee; // Επιτροπή

    public function get_receiptcommittee() {
        return $this->_receiptcommittee;
    }

    public function set_receiptcommittee($_receiptcommittee) {
        $_receiptcommittee->set_aitisi($this);
        $_receiptcommittee->set_project($this->get_project());
        $this->_receiptcommittee = $_receiptcommittee;
    }

    protected function updateProject() {
        
    }

    public function onApproval() {
        $this->_committee->set_active(true);
    }

    public function onRejection() {
        $this->_committee->set_active(false);
    }

    public function hasOwnTitle() {
        return false;
    }
}
?>