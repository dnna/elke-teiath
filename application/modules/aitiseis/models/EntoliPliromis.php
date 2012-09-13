<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.entolipliromis")
 */
class Aitiseis_Model_EntoliPliromis extends Aitiseis_Model_AitisiBase {
    const type = "Εντολή Πληρωμής";
    const formclass = "Aitiseis_Form_EntoliPliromis";
    const template = "O01-EntoliPliromis";

    /**
     * @ManyToOne (targetEntity="Erga_Model_SubProject")
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     */
    protected $_subproject;
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectEmployee")
     * @JoinColumn (name="recipientauthor", referencedColumnName="recordid")
     */
    protected $_recipientauthor; // Συντάκτης
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubItems_SubProjectContractor")
     * @JoinColumn (name="recipientcontractor", referencedColumnName="recordid")
     */
    protected $_recipientcontractor; // Ανάδοχος
    /**
     * @Column (name="amount", type="greekfloat")
     */
    protected $_amount; // By default το ποσό του παραδοτέου (αν αφορά ανάδοχο ή αν υπάρχει μόνο 1 συντάκτης, αλλιώς empty)
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_EntoliPliromis_Deliverable", mappedBy="_entolipliromis", orphanRemoval=true, cascade={"all"})
     * @var Aitiseis_Model_EntoliPliromis_Deliverable
     */
    protected $_deliverables;
    /**
     * @Column (name="type", type="integer")
     */
    protected $_type; // Είδος Πληρωμής
    const TYPE_1 = 'Αμοιβή';
    const TYPE_2 = 'Πληρωμή';
    const TYPE_3 = 'Προκαταβολή';
    const TYPE_4 = 'Απόδοση';
    /**
     * @Column (name="vouchertype", type="integer")
     */
    protected $_vouchertype; // Είδος Παραστατικού
    const VOUCHERTYPE_1 = 'Δελτίο Παροχής Υπηρεσιών';
    const VOUCHERTYPE_2 = 'Κατάσταση Αμοιβών';
    const VOUCHERTYPE_3 = 'Απόδειξη Επαγγελματικής Δαπάνης';
    /**
     * @Column (name="subtype", type="integer")
     */
    protected $_subtype; // Υποτύπος
    const SUBTYPE_1 = 'Αναλώσιμα';
    const SUBTYPE_2 = 'Επισκευή - Συντήρηση';
    const SUBTYPE_3 = 'Γενικά Έξοδα';
    const SUBTYPE_4 = 'Εξοπλισμός (όργανα – υλικά)';
    const SUBTYPE_5 = 'Συνεργαζόμενα Ιδρύματα';
    const SUBTYPE_6 = 'Λοιπά Έξοδα';
    const SUBTYPE_7 = 'Μετακινήσεις - Έξοδα Ταξιδιών';
    const SUBTYPE_8 = 'Παροχές Τρίτων (Ασφάλιστρα, Τηλεπικοινωνίες, Ενοίκια  κλπ)';
    /**
     * @Column (name="reasoning", type="string")
     */
    protected $_reasoning; // Αιτιολογία (Συνοπτικά)
    /**
     * @Column (name="acccode", type="string")
     */
    protected $_acccode; // Κωδικός Λογιστικής
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_ExpenditureCategory")
     * @JoinColumn (name="expenditurecategoryid", referencedColumnName="id")
     * @var Application_Model_Lists_ExpenditureCategory
     */
    protected $_expenditurecategory; // Κατηγορία δαπάνης (από την σχετική λίστα)
    /**
     * @Column (name="paymentmethod", type="integer")
     */
    protected $_paymentmethod; // Τρόπος Πληρωμής
    const PAYMENTMETHOD_1 = 'Επιταγή';
    const PAYMENTMETHOD_2 = 'Κατάθεση σε τραπεζικό λογαριασμό δικαιούχου';
    /**
     * @Column (name="recbankaccount", type="string")
     */
    protected $_recbankaccount; // Τραπεζικός λογαριασμός δικαιούχου

    protected function updateProject() {}
    public function onApproval() {}

    public function onRejection() {}

    public function hasOwnTitle() {
        return true;
    }

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject($_subproject) {
        $this->_subproject = $_subproject;
        $this->set_project($_subproject->get_parentproject());
    }

    public function get_title() {
        if($this->get_recipientauthor() != null) {
            return $this->get_recipientauthor()->__toString();
        } else if($this->get_recipientcontractor()) {
            return $this->get_recipientcontractor()->__toString();
        } else {
            return 'TITLE_PENDING';
        }
    }

    public function get_recipientauthor() {
        return $this->_recipientauthor;
    }

    public function set_recipientauthor($_recipientauthor) {
        $this->_recipientauthor = $_recipientauthor;
    }

    public function get_recipientcontractor() {
        return $this->_recipientcontractor;
    }

    public function set_recipientcontractor($_recipientcontractor) {
        $this->_recipientcontractor = $_recipientcontractor;
    }

    public function get_amount() {
        return $this->_amount;
    }

    public function set_amount($_amount) {
        $this->_amount = $_amount;
    }

    public function get_deliverables() {
        return $this->_deliverables;
    }

    public function set_deliverables($_deliverables) {
        $this->_deliverables = $_deliverables;
    }

    public function get_type() {
        return $this->_type;
    }

    public function set_type($_type) {
        $types = self::getConstantAsArray('TYPE');
        if(array_key_exists($_type, $types)) {
            $this->_type = $_type;
        } else {
            throw new Exception('Ο συγκεκριμένος τύπος δεν υπάρχει.');
        }
    }

    public function get_vouchertype() {
        return $this->_vouchertype;
    }

    public function set_vouchertype($_vouchertype) {
        $vouchertypes = self::getConstantAsArray('VOUCHERTYPE');
        if(array_key_exists($_vouchertype, $vouchertypes)) {
            $this->_vouchertype = $_vouchertype;
        } else {
            throw new Exception('Το συγκεκριμένο είδος παραστατικού δεν υπάρχει.');
        }
    }

    public function get_subtype() {
        return $this->_subtype;
    }

    public function set_subtype($_subtype) {
        $subtypes = self::getConstantAsArray('SUBTYPE');
        if(array_key_exists($_subtype, $subtypes)) {
            $this->_subtype = $_subtype;
        } else {
            throw new Exception('Ο συγκεκριμένος υποτύπος δεν υπάρχει.');
        }
    }

    public function get_reasoning() {
        return $this->_reasoning;
    }

    public function set_reasoning($_reasoning) {
        $this->_reasoning = $_reasoning;
    }

    public function get_acccode() {
        return $this->_acccode;
    }

    public function set_acccode($_acccode) {
        $this->_acccode = $_acccode;
    }

    public function get_expenditurecategory() {
        return $this->_expenditurecategory;
    }

    public function set_expenditurecategory($_expenditurecategory) {
        $this->_expenditurecategory = $_expenditurecategory;
    }

    public function get_paymentmethod() {
        return $this->_paymentmethod;
    }

    public function set_paymentmethod($_paymentmethod) {
        $paymentmethods = self::getConstantAsArray('PAYMENTMETHOD');
        if(array_key_exists($_paymentmethod, $paymentmethods)) {
            $this->_paymentmethod = $_paymentmethod;
        } else {
            throw new Exception('Ο συγκεκριμένος τρόπος πληρωμής δεν υπάρχει.');
        }
    }

    public function get_recbankaccount() {
        return $this->_recbankaccount;
    }

    public function set_recbankaccount($_recbankaccount) {
        $this->_recbankaccount = $_recbankaccount;
    }
    
    public static function getConstantAsArray($constantname) {
        $i = 1;
        $result = array();
        while(defined(__CLASS__.'::'.$constantname.'_'.$i)) {
            $result[$i] = constant(__CLASS__.'::'.$constantname.'_'.$i);
            $i++;
        }
        return $result;
    }
}
?>