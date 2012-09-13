<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.onomastikikatastasi")
 */
class Aitiseis_Model_OnomastikiKatastasi extends Aitiseis_Model_AitisiBase {
    const type = "Ονομαστική Κατάσταση Απασχολούμενων Έργου";
    const formclass = "Aitiseis_Form_OnomastikiKatastasi";
    const template = "D03-OnomastikiKatastasiApasxoloumenwnErgou";
    protected $_availableActions = array(self::ACTION_EXPORT);
    // Ονομαστική Κατάσταση Απασχολούμενων
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_OnomastikiKatastasi_AitisiEmployee", mappedBy="_aitisi", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_OnomastikiKatastasi_AitisiEmployee
     */
    protected $_employees; // Ονομαστική κατάσταση απασχολούμενων στο έργο

    /**
     * @return Aitiseis_Model_SubItems_Employee
     */
    public function get_employees() {
        return $this->_employees;
    }

    public function set_employees($_employees) {
        $this->_employees = $_employees;
    }

    protected function updateProject() {
        $vars = $this->toArray(null, true);
        if($this->_project->get_iscomplex() != 0) {
            throw new Exception('Οι απασχολούμενοι δεν μπόρεσαν να εξαχθούν γιατί το συνδεδεμένο έργο είναι σύνθετο.');
        }
        $this->_project->getVirtualSubProject()->setOptions($vars, true);
        $this->_project->save();
        return $this->_project;
    }

    public function onApproval() {
        
    }

    public function onRejection() {
        
    }

    public function hasOwnTitle() {
        return false;
    }
}
?>