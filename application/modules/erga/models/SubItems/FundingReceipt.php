<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.fundingreceipts")
 */
class Erga_Model_SubItems_FundingReceipt extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectFinancialDetails", inversedBy="_fundingreceipts")
     * @JoinColumn (name="financialdetailsid", referencedColumnName="financialdetailsid")
     */
    protected $_financialdetails;
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_FundingAgency")
     * @JoinColumn (name="fundingagencyid", referencedColumnName="recordid")
     */
    protected $_fundingagency;
    /**
     * @Column (name="date", type="date")
     * @var EDateTime
     */
    protected $_date;
    /** @Column (name="amount", type="greekfloat") */
    protected $_amount;
    
    public function get_financialdetails() {
        return $this->_financialdetails;
    }

    public function set_financialdetails($_financialdetails) {
        $this->_financialdetails = $_financialdetails;
    }

    public function get_fundingagency() {
        return $this->_fundingagency;
    }

    public function set_fundingagency($_fundingagency) {
        $this->_fundingagency = $_fundingagency;
    }

    public function get_date() {
        return $this->_date;
    }

    public function set_date($_date) {
        $this->_date = EDateTime::create($_date);
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }
    
    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectFinancialDetails) {
            $this->set_financialdetails($owner);
        }
    }
}
?>