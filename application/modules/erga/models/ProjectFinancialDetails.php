<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.projectfinancialdetails")
 */
class Erga_Model_ProjectFinancialDetails extends Dnna_Model_Object {
    // Οικονομικά Στοιχεία
    /**
     * @Id
     * @Column (name="financialdetailsid", type="integer")
     * @GeneratedValue
     */
    protected $_financialdetailsid;
    /**
     * @OneToOne (targetEntity="Erga_Model_Project", inversedBy="_financialdetails")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     */
    protected $_project;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_FundingAgency", mappedBy="_financialdetails", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_FundingAgency
     */
    protected $_fundingagencies;
    /** @Column (name="sae", type="string") */
    protected $_sae; // ΣΑΕ
    /**
     * @Column (name="nationalparticipation", type="greekpercentage")
     */
    protected $_nationalparticipation;
    /**
     * @Column (name="europeanparticipation", type="greekpercentage")
     */
    protected $_europeanparticipation;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_FundingFramework")
     * @JoinColumn (name="fundingframeworkid", referencedColumnName="fundingframeworkid")
     * @var Application_Model_Lists_FundingFramework
     */
    protected $_fundingframework; // Πλαίσιο Χρηματοδότησης
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_OpProgramme")
     * @JoinColumn (name="opprogrammeid", referencedColumnName="opprogrammeid")
     * @var Application_Model_Lists_OpProgramme
     */
    protected $_opprogramme; // Επιχειρησιακό Πρόγραμμα
    /** @Column (name="axis", type="string") */
    protected $_axis; // Άξονας
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_FundingReceipt", mappedBy="_financialdetails", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_FundingReceipt
     */
    protected $_fundingreceipts; // Πίνακας με Χρηματοδοτήσεις
    /**
     * @Column (name="budget", type="greekfloat")
     * @FormFieldLabel Προϋπολογισμός
     */
    protected $_budget;
    /**
     * @Column (name="budgetfpa", type="greekfloat")
     */
    protected $_budgetfpa;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Bank")
     * @JoinColumn (name="bankid", referencedColumnName="id")
     * @var Application_Model_Lists_Bank
     */
    protected $_bank;
    /**
     * @Column (name="iban", type="string")
     */
    protected $_iban;
    /**
     * @Column (name="financialenddate", type="date")
     */
    protected $_financialenddate;
    /**
     * @OneToMany (targetEntity="Erga_Model_SubItems_BudgetItem", mappedBy="_financialdetails", orphanRemoval=true, cascade={"all"})
     * @var Erga_Model_SubItems_BudgetItem
     */
    protected $_budgetitems; // Αναλυτικός Προϋπολογισμός

    protected $_isvirtual = 0;

    public function get_financialdetailsid() {
        return $this->_financialdetailsid;
    }

    public function set_financialdetailsid($_financialdetailsid) {
        $this->_financialdetailsid = $_financialdetailsid;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project($_project) {
        $this->_project = $_project;
    }

    public function get_fundingagencies() {
        return $this->_fundingagencies;
    }

    public function set_fundingagencies($_fundingagencies) {
        $this->_fundingagencies = $_fundingagencies;
    }

    public function get_fundingagenciesAsString() {
        $string = '';
        $i = 1;
        foreach($this->_fundingagencies as $curAgency) {
            $string .= $curAgency->get_agency()->get_name();
            if($i < $this->_fundingagencies->count()) {
                $string .= ', ';
            }
            $i++;
        }
        return $string;
    }

    public function get_sae() {
        return $this->_sae;
    }

    public function set_sae($_sae) {
        if($_sae == "") {
            $this->_sae = null;
        } else {
            $this->_sae = $_sae;
        }
    }

    public function get_nationalparticipation() {
        return $this->_nationalparticipation;
    }

    public function set_nationalparticipation($_nationalparticipation) {
        if($_nationalparticipation == "") {
            $this->_nationalparticipation = null;
        } else {
            $this->_nationalparticipation = $_nationalparticipation;
        }
    }

    public function get_europeanparticipation() {
        return $this->_europeanparticipation;
    }

    public function set_europeanparticipation($_europeanparticipation) {
        if($_europeanparticipation == "") {
            $this->_europeanparticipation = null;
        } else {
            $this->_europeanparticipation = $_europeanparticipation;
        }
    }

    public function get_fundingframework() {
        if($this->_fundingframework != null) {
            return $this->_fundingframework;
        } else {
            $fundingframework = new Application_Model_Lists_FundingFramework();
            $fundingframework->set_fundingframeworkname('-');
            return $fundingframework;
        }
    }

    public function set_fundingframework($_fundingframework) {
        $this->_fundingframework = $_fundingframework;
    }

    public function get_opprogramme() {
        if($this->_opprogramme != null) {
            return $this->_opprogramme;
        } else {
            $opprogramme = new Application_Model_Lists_OpProgramme();
            $opprogramme->set_opprogrammename('-');
            return $opprogramme;
        }
    }

    public function set_opprogramme($_opprogramme) {
        $this->_opprogramme = $_opprogramme;
    }

    public function get_axis() {
        return $this->_axis;
    }

    public function set_axis($_axis) {
        $this->_axis = $_axis;
    }

    public function get_fundingreceipts() {
        return $this->_fundingreceipts;
    }

    public function set_fundingreceipts($_fundingreceipts) {
        $this->_fundingreceipts = $_fundingreceipts;
    }

    public function get_budget() {
        return $this->_budget;
    }

    public function set_budget($_budget) {
        $this->_budget = $_budget;
    }

    public function get_budgetfpa() {
        return $this->_budgetfpa;
    }

    public function set_budgetfpa($_budgetfpa) {
        $this->_budgetfpa = $_budgetfpa;
    }
    
    public function get_budgetwithfpa() {
        if($this->get_budget() != null && $this->get_budgetfpa() != null) {
            $budget = Zend_Locale_Format::getNumber($this->get_budget(),
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       );
            $budgetfpa = Zend_Locale_Format::getNumber($this->get_budgetfpa(),
                                        array('precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale'))
                                       );
            return Zend_Locale_Format::toNumber($budget + $budgetfpa,
                                        array(
                                              'precision' => 2,
                                              'locale' => Zend_Registry::get('Zend_Locale')));
        } else if($this->get_budget() != null) {
            return $this->get_budget();
        } else {
            return null;
        }
    }

    public function get_bank() {
        return $this->_bank;
    }

    public function set_bank($_bank) {
        $this->_bank = $_bank;
    }

    public function get_iban() {
        return $this->_iban;
    }

    public function set_iban($_iban) {
        $this->_iban = $_iban;
    }

    public function get_financialenddate() {
        return $this->_financialenddate;
    }

    public function set_financialenddate($_financialenddate) {
        $this->_financialenddate = EDateTime::create($_financialenddate);
    }

    public function get_budgetitems() {
        return $this->_budgetitems;
    }

    public function set_budgetitems($_budgetitems) {
        $this->_budgetitems = $_budgetitems;
    }

    public function get_isvirtual() {
        return $this->_isvirtual;
    }

    public function set_isvirtual($_isvirtual) {
        $this->_isvirtual = $_isvirtual;
    }

    public function setOwner($owner) {
        $this->set_project($owner);
    }
}
?>