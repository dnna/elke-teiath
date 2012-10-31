<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.oikonomikisdiaxeirisisergou")
 */
class Aitiseis_Model_OikonomikiDiaxeirisi_Ergou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Έγκρισης Ανάληψης Οικονομικής Διαχείρισης Έργου";
    const formclass = "Aitiseis_Form_OikonomikiDiaxeirisiErgou";
    const template = "D01b-AitisiEgkrisisAnalipsisOikonomikisDiaxeirisisErgou";
    protected $_availableActions = array(self::ACTION_EXPORT);

    // Οικονομική διαχείριση έργου
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_OikonomikiDiaxeirisi_Ergou_Partner", mappedBy="_aitisi", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_OikonomikiDiaxeirisi_Partner
     */
    protected $_partners; // Συνεργαζόμενοι φορείς
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_ProjectCategory")
     * @JoinColumn (name="category", referencedColumnName="id")
     * @var Application_Model_Lists_ProjectCategory
     */
    protected $_category;
    /**
     * @Column (name="totalbudget", type="greekfloat")
     */
    protected $_totalbudget;
    /**
     * @Column (name="teibudget", type="greekfloat")
     */
    protected $_teibudget;
    protected $__cs; // Η κατηγορία που έχει επιλεχθεί σε μορφή πίνακα. Δεν αποθηκεύται στη βάση δεδομένων.
    /** @Column (name="elkededuction", type="greekfloat") */
    protected $_elkededuction;
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_OikonomikiDiaxeirisi_Ergou_BudgetItem", mappedBy="_aitisioikonomikisdiaxeirisis", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_OikonomikiDiaxeirisi_BudgetItem
     */
    protected $_budgetitems; // Αναλυτικός Προϋπολογισμός

    public function get_partners() {
        return $this->_partners;
    }

    public function set_partners($_partners) {
        $this->_partners = $_partners;
    }

    public function get_category() {
        if($this->_category != null) {
            return $this->_category;
        } else {
            return new Application_Model_Lists_ProjectCategory();
        }
    }

    public function set_category($_category) {
        $this->_category = $_category;
    }

    public function get__cs() {
        for($i = 0; $i < 5; $i++) {
            if($this->get_category()->get_id() == $i) {
                $this->__cs[$i] = 'X';
            }
        }
        return $this->__cs;
    }

    public function get_totalbudget() {
        return $this->_totalbudget;
    }

    public function set_totalbudget($_totalbudget) {
        $this->_totalbudget = $_totalbudget;
    }

    public function get_teibudget() {
        return $this->_teibudget;
    }

    public function set_teibudget($_teibudget) {
        $this->_teibudget = $_teibudget;
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
        $this->_project->get_basicdetails()->set_category($this->_category);
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
        return false;
    }
}
?>