<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Praktika")
 */
class Praktika_Model_Committee_Enstaseon extends Praktika_Model_CommitteeBase {
    const type = "Επιτροπή Ενστάσεων";
    const formclass = "Praktika_Form_Committee_Enstaseon";
    /**
     * @OneToOne (targetEntity="Aitiseis_Model_OrismosEpitropisDiagonismou", inversedBy="_objectioncommittee")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_OrismosEpitropisDiagonismou
     */
    protected $_aitisi;

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }
}
?>