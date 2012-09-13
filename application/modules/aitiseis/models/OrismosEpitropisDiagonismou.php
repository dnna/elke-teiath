<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.orismosepitropisdiagonismou")
 */
class Aitiseis_Model_OrismosEpitropisDiagonismou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση για Ορισμό Επιτροπής Διαγωνισμού";
    const formclass = "Aitiseis_Form_OrismosEpitropisDiagonismou";
    const template = "AitisiOrismouEpitropisDiagonismou";

    /**
     * @OneToOne (targetEntity="Erga_Model_SubProject")
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     * @var Erga_Model_SubProject
     */
    protected $_subproject;
    /**
     * @OneToOne (targetEntity="Praktika_Model_Competition", cascade={"persist"}, mappedBy="_aitisi")
     * @var Praktika_Model_Competition
     */
    protected $_competition; // Διαγωνισμός
    /**
     * @OneToOne (targetEntity="Praktika_Model_Committee_Diagonismou", cascade={"persist"}, mappedBy="_aitisi")
     * @var Praktika_Model_Committee_Diagonismou
     */
    //protected $_competitioncommittee; // Επιτροπή διαγωνισμού
    /**
     * @OneToOne (targetEntity="Praktika_Model_Committee_Enstaseon", cascade={"persist"}, mappedBy="_aitisi")
     * @var Praktika_Model_Committee_Enstaseon
     */
    //protected $_objectioncommittee; // Επιτροπή ενστάσεων

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject(Erga_Model_SubProject $_subproject) {
        $this->set_project($_subproject->get_parentproject());
        $this->_subproject = $_subproject;
    }

    public function get_competition() {
        return $this->_competition;
    }

    public function set_competition($_competition) {
        $this->_competition = $_competition;
    }

    public function get_competitioncommittee() {
        return $this->_competitioncommittee;
    }

    public function set_competitioncommittee(Praktika_Model_Committee_Diagonismou $_competitioncommittee) {
        $_competitioncommittee->set_aitisi($this);
        $_competitioncommittee->set_competition($this->get_competition());
        $this->_competitioncommittee = $_competitioncommittee;
    }

    public function get_objectioncommittee() {
        return $this->_objectioncommittee;
    }

    public function set_objectioncommittee(Praktika_Model_Committee_Enstaseon $_objectioncommittee) {
        $_objectioncommittee->set_aitisi($this);
        $this->_objectioncommittee = $_objectioncommittee;
    }

    protected function updateProject() {
        
    }

    public function onApproval() {
        if(isset($this->_competitioncommittee)) {
            $this->_competitioncommittee->set_active(true);
        }
        if(isset($this->_objectioncommittee)) {
            $this->_objectioncommittee->set_active(true);
        }
        if(($competition = $this->_subproject->get_competition()) != null && $competition != $this->_competition) {
            /*// Το υποέργο έχει διαγωνισμό, οπότε τον σβήνουμε πριν προχωρήσουμε
            $oldoptions = $competition->getOptions(false, array('ignoreobjects' => true));
            $newoptions = $this->_competition->getOptions(false, array('ignoreobjects' => true));
            $options = array_merge($oldoptions, $newoptions);
            $this->_competition->setOptions($options);*/
            $competition->remove();
        }
        // Το υποέργο δεν έχει διαγωνισμό οπότε του βάζουμε αυτόν που δημιουργήθηκε
        $this->get_competition()->set_subproject($this->_subproject);
        $this->_subproject->set_subprojectdirectlabor(0);
    }

    public function onRejection() {
        if(isset($this->_competitioncommittee)) {
            $this->_competitioncommittee->set_active(false);
        }
        if(isset($this->_objectioncommittee)) {
            $this->_objectioncommittee->set_active(false);
        }
    }

    public function hasOwnTitle() {
        return false;
    }
}
?>