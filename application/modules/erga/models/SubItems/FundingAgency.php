<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="elke_erga.financialdetails_fundingagencies")
 */
class Erga_Model_SubItems_FundingAgency extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_ProjectFinancialDetails", inversedBy="_fundingreceipts")
     * @JoinColumn (name="financialdetailsid", referencedColumnName="financialdetailsid")
     */
    protected $_financialdetails;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency", cascade={"persist"})
     * @JoinColumn (name="agencyid", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_agency;

    public function get_financialdetails() {
        return $this->_financialdetails;
    }

    public function set_financialdetails($_financialdetails) {
        $this->_financialdetails = $_financialdetails;
    }

    public function get_agency() {
        return $this->_agency;
    }

    public function set_agency($_agency) {
        $this->_agency = $_agency;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_ProjectFinancialDetails) {
            $this->set_financialdetails($owner);
        }
    }
}
?>