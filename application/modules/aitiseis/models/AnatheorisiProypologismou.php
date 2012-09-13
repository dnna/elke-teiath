<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.anatheorisisproypologismou")
 */
class Aitiseis_Model_AnatheorisiProypologismou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Αναθεώρησης Προϋπολογισμού";
    const formclass = "Aitiseis_Form_AnatheorisiProypologismou";
    const template = "D02-AitisiAnatheorisisProypologismou";
    protected $_availableActions = array();
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_AnatheorisiProypologismou_BudgetItem", mappedBy="_aitisianatheorisisproypologismou", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_AnatheorisiProypologismou_BudgetItem
     */
    protected $_budgetitems; // Αναλυτικός Προϋπολογισμός

    /**
     * @return Aitiseis_Model_AnatheorisiProypologismou_BudgetItem
     */
    public function get_budgetitems() {
        return $this->_budgetitems;
    }

    public function set_budgetitems($_budgetitems) {
        $this->_budgetitems = $_budgetitems;
    }

    protected function updateProject() {}

    public function onApproval() {}

    public function onRejection() {}
    
    public function hasOwnTitle() {
        return false;
    }
}
?>