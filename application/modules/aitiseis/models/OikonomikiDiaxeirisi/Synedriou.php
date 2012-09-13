<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.oikonomikisdiaxeirisissynedriou")
 */
class Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Έγκρισης Ανάληψης Οικονομικής Διαχείρισης Συνεδρίου";
    const formclass = "Aitiseis_Form_OikonomikiDiaxeirisiSynedriou";
    const template = "D01c-AitisiEgkrisisAnalipsisOikonomikisDiaxeirisisSynedriou";
    protected $_availableActions = array();

    // Οικονομική διαχείριση έργου
    /**
     * @Column (name="totalbudget", type="greekfloat")
     */
    protected $_totalbudget;
    /** @Column (name="elkededuction", type="greekfloat") */
    protected $_elkededuction;
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou_BudgetItem", mappedBy="_aitisioikonomikisdiaxeirisis", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_OikonomikiDiaxeirisi_BudgetItem
     */
    protected $_budgetitems; // Αναλυτικός Προϋπολογισμός

    public function get_totalbudget() {
        return $this->_totalbudget;
    }

    public function set_totalbudget($_totalbudget) {
        $this->_totalbudget = $_totalbudget;
    }

    public function get_elkededuction() {
        return $this->_elkededuction;
    }

    public function set_elkededuction($_elkededuction) {
        $this->_elkededuction = $_elkededuction;
    }

    /**
     * @return Aitiseis_Model_OikonomikiDiaxeirisi_BudgetItem
     */
    public function get_budgetitems() {
        return $this->_budgetitems;
    }

    public function set_budgetitems($_budgetitems) {
        $this->_budgetitems = $_budgetitems;
    }

    public function onApproval() {}

    public function onRejection() {}

    protected function updateProject() {
        $vars = $this->toArray(null, true);

        $this->_project->get_financialdetails()->setOptions($vars);
        $this->_project->get_financialdetails()->set_budget($this->_totalbudget);
        $this->_project->get_position()->setOptions($vars);
        if($this->_parentaitisi->get_contractor() != null) {
            $this->_project->get_position()->set_teirole(3);
            $this->_project->get_position()->set_anadoxos($this->_parentaitisi->get_contractor());
        } else {
            $this->_project->get_position()->set_teirole(1);
            $this->_project->get_position()->set_anadoxos(null);
        }

        $this->_project->save();
        return $this->_project;
    }

    public function hasOwnTitle() {
        return true;
    }
}
?>