<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.daneismou")
 */
class Aitiseis_Model_Daneismou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Δανεισμού Έργου";
    const formclass = "Aitiseis_Form_Daneismou";
    const template = "D08-AitisiDaneismouErgou";
    protected $_availableActions = array();
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_Daneismou_LoanItem", mappedBy="_aitisidaneismou", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_Daneismou_LoanItem
     */
    protected $_loanitems; // Αναλυτικός Προϋπολογισμός
    
    protected $_budgetitems;
    protected $_amoibes;

    protected $_sum; // Σύνολο

    public function get_loanitems() {
        return $this->_loanitems;
    }

    public function set_loanitems($_loanitems) {
        $this->_loanitems = $_loanitems;
    }

    public function get_sum() {
        $this->_sum = 0;
		if($this->get_loanitems() != null) {
			foreach($this->get_loanitems() as $curLoanItem) {
				$this->_sum = $this->_sum + $curLoanItem->get_amount();
			}
		}
        return $this->_sum;
    }

    public function get_budgetitems() {
        $this->_budgetitems = array();
		if($this->get_loanitems() != null) {
			foreach($this->get_loanitems() as $curLoanItem) {
				if(get_class($curLoanItem) === 'Aitiseis_Model_Daneismou_LoanItemBudgetItem') {
					array_push($this->_budgetitems, $curLoanItem);
				}
			}
		}
        return $this->_budgetitems;
    }

    public function get_amoibes() {
        $this->_amoibes = array();
		if($this->get_loanitems() != null) {
			foreach($this->get_loanitems() as $curLoanItem) {
				if(get_class($curLoanItem) === 'Aitiseis_Model_Daneismou_LoanItemEmployee' || get_class($curLoanItem) === 'Aitiseis_Model_Daneismou_LoanItemContractor') {
					array_push($this->_amoibes, $curLoanItem);
				}
			}
		}
        return $this->_amoibes;
    }

    protected function updateProject() {}

    public function onApproval() {}

    public function onRejection() {}

    public function hasOwnTitle() {
        return false;
    }

    // Κατανέμει τις δαπάνες στις κατάλληλες κλάσεις (budgetitem, employee ή
    // contractor).
    public function setOptions(array $options, $ignoreisvisible = false) {
        if(isset($options['default']) && count($options['default']) > 0) {
            $options = array_merge($options, $options['default']);
            unset($options['default']);
        }
        if(isset($options['loanitems'])) {
            if(isset($options['loanitems']['default']) && count($options['loanitems']['default']) > 0) {
                $options['loanitems'] = array_merge($options['loanitems'], $options['loanitems']['default']);
                unset($options['loanitems']['default']);
            }
            foreach($options['loanitems'] as &$curLoanItem) {
                if(isset($curLoanItem['budgetitem']) && $curLoanItem['budgetitem'] != '') {
                    $curLoanItem['classname'] = 'Aitiseis_Model_Daneismou_LoanItemBudgetItem';
                } else if(isset($curLoanItem['employee']) && $curLoanItem['employee'] != '') {
                    $curLoanItem['classname'] = 'Aitiseis_Model_Daneismou_LoanItemEmployee';
                } else if(isset($curLoanItem['contractor']) && $curLoanItem['contractor'] != '') {
                    $curLoanItem['classname'] = 'Aitiseis_Model_Daneismou_LoanItemContractor';
                } else {
                    throw new Exception('Σφάλμα κατά την εύρεση του τύπου δαπάνης.');
                }
            }
        }
        return parent::setOptions($options, $ignoreisvisible);
    }
}
?>