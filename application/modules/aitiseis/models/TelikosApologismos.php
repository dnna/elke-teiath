<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.telikouapologismou")
 */
class Aitiseis_Model_TelikosApologismos extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Έγκρισης Τελικού Επιστημονικού Απολογισμού Έργου";
    const formclass = "Aitiseis_Form_TelikosApologismos";
    const template = "D11-TelikosApologismosErgou";

    /**
     * @Column (name="description", type="string")
     */
    protected $_description;
    /**
     * @Column (name="publications", type="integer")
     */
    protected $_publications;
    /**
     * @Column (name="anakoinwseis", type="integer")
     */
    protected $_anakoinwseis;
    /**
     * @Column (name="anafores", type="integer")
     */
    protected $_anafores;
    /**
     * @Column (name="alla", type="string")
     */
    protected $_alla;

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
    }
    protected function updateProject() {}
    public function onApproval() {}

    public function onRejection() {}

    public function hasOwnTitle() {
        return false;
    }

    public function set_publications($publications)
    {
        $this->_publications = $publications;
    }

    public function get_publications()
    {
        return $this->_publications;
    }

    public function set_anafores($anafores)
    {
        $this->_anafores = $anafores;
    }

    public function get_anafores()
    {
        return $this->_anafores;
    }

    public function set_anakoinwseis($anakoinwseis)
    {
        $this->_anakoinwseis = $anakoinwseis;
    }

    public function get_anakoinwseis()
    {
        return $this->_anakoinwseis;
    }

    public function set_alla($alla)
    {
        $this->_alla = $alla;
    }

    public function get_alla()
    {
        return $this->_alla;
    }
}
?>