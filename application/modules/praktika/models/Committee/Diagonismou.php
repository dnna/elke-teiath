<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Praktika")
 */
class Praktika_Model_Committee_Diagonismou extends Praktika_Model_CommitteeBase {
    const type = "Επιτροπή Διενέργειας Διαγωνισμού";
    const formclass = "Praktika_Form_Committee_Diagonismou";
    /**
     * @OneToOne (targetEntity="Aitiseis_Model_OrismosEpitropisDiagonismou", inversedBy="_competitioncommittee")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_OrismosEpitropisDiagonismou
     */
    protected $_aitisi;
    /**
     * @ManyToOne (targetEntity="Praktika_Model_Competition", inversedBy="_committees")
     * @JoinColumn (name="competitionid", referencedColumnName="recordid")
     * @var Praktika_Model_Competition
     */
    protected $_competition;

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }

    public function get_competition() {
        return $this->_competition;
    }

    public function set_competition($_competition) {
        $this->_competition = $_competition;
    }
}
?>