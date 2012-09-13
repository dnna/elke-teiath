<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Competitions") @Table(name="elke_praktika.competitions")
 */
class Praktika_Model_Competition extends Application_Model_SubObject {
    /**
     * @ManyToOne (targetEntity="Erga_Model_SubProject", inversedBy="_competition")
     * @JoinColumn (name="subprojectid", referencedColumnName="subprojectid")
     * @var Erga_Model_SubProject
     */
    protected $_subproject; // Υποέργο
    /**
     * @OneToOne (targetEntity="Aitiseis_Model_OrismosEpitropisDiagonismou", inversedBy="_competition")
     * @JoinColumn (name="aitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_OrismosEpitropisDiagonismou
     */
    protected $_aitisi;
    /**
     * @OneToMany (targetEntity="Praktika_Model_Committee_Diagonismou", mappedBy="_competition")
     * @var Praktika_Model_Committee_Diagonismou
     */
    protected $_committees;
    /** @Column (name="competitiontype", type="integer") */
    protected $_competitiontype = '1'; // Είδος διαγωνισμού

    /** @Column (name="procurementtype", type="integer") */
    protected $_procurementtype = '1';
    /**
     * @OneToOne (targetEntity="Application_Model_Consultant", cascade={"all"}, orphanRemoval=true, inversedBy="_astechnicalconsultant")
     * @JoinColumn (name="technicalconsultantid", referencedColumnName="id")
     */
    protected $_technicalconsultant; // Ονοματεπώνυμο και Ιδιότητα Συμβούλου για Τεχνικά Θέματα
    /**
     * @Column (name="offerslanguage", type="string")
     * @FormFieldLabel Γλώσσα/ες κειμένου προσφορών
     */
    protected $_offerslanguage; // Γλώσσα/ες κειμένου προσφορών
    /**
     * @Column (name="offerssubmissionlocation", type="string")
     * @FormFieldLabel Τόπος κατάθεσης προσφορών
     */
    protected $_offerssubmissionlocation; // Τόπος κατάθεσης προσφορών
    /**
     * @Column (name="offersopeningdate", type="date")
     * @FormFieldLabel Ημ/νία ανοίγματος προσφορών
     */
    protected $_offersopeningdate; // Ημ/νία ανοίγματος προσφορών
    /**
     * @Column (name="execlocation", type="string")
     * @FormFieldLabel Τόπος παράδοσης/εκτέλεσης των προμηθευόμενων αγαθών/υπηρεσιών
     */
    protected $_execlocation; // Τόπος παράδοσης/εκτέλεσης των προμηθευόμενων αγαθών/υπηρεσιών
    /**
     * @Column (name="execduration", type="integer")
     * @FormFieldLabel Χρόνος παράδοσης (μήνες) των προμηθευομένων αγαθών
     */
    protected $_execduration; // Χρόνος παράδοσης (μήνες) των προμηθευομένων αγαθών
    /**
     * @Column (name="paymentmethod", type="string")
     * @FormFieldLabel Τρόπος πληρωμής
     */
    protected $_paymentmethod; // Τρόπος πληρωμής
    /**
     * @OneToOne (targetEntity="Application_Model_Consultant", cascade={"all"}, orphanRemoval=true, inversedBy="_asresponsibleperson")
     * @JoinColumn (name="responsibleid", referencedColumnName="id")
     */
    protected $_responsibleperson; // Υπεύθυνος διαγωνισμού για πληροφορίες/προδιαγραφές

    /** @Column (name="refnumassignment", type="string") */
    protected $_refnumassignment; // Αρ. Πρωτ. Ανάθεσης
    /** @Column (name="assignmentdate", type="date") */
    protected $_assignmentdate; // Ημερομνία Ανάθεσης
    /** @Column (name="refnumnotice", type="string") */
    protected $_refnumnotice; // Αρ. Πρωτ. Προκήρυξης
    /** @Column (name="noticedate", type="date") */
    protected $_noticedate; //Ημερομνία Προκήρυξης
    /** @Column (name="execdate", type="date") */
    protected $_execdate; // Ημερομνία Διενέργειας
    /** @Column (name="refnumaward", type="string") */
    protected $_refnumaward; // Απόφαση Κατακύρωσης
    /** @Column (name="awarddate", type="date") */
    protected $_awarddate; // Ημερομνία Κατακύρωσης

    const COMPETITIONTYPE_1 = 'Ανάθεση';
    const COMPETITIONTYPE_2 = 'Πρόχειρος';
    const COMPETITIONTYPE_3 = 'Ανοιχτός/Τακτικός';
    const COMPETITIONTYPE_4 = 'Ανοιχτός/Διεθνής';

    const PROCUREMENTTYPE_1 = 'Αγαθά';
    const PROCUREMENTTYPE_2 = 'Υπηρεσίες';

    public function get_subproject() {
        return $this->_subproject;
    }

    public function set_subproject($_subproject) {
        $this->_subproject = $_subproject;
        if($_subproject != null) {
            $_subproject->set_competition($this);
        }
    }
    
    public function get_project() {
        if($this->get_subproject() != null) {
            return $this->get_subproject()->get_parentproject();
        } else if($this->get_aitisi() != null) {
            return $this->get_aitisi()->get_project();
        } else {
            throw new Exception('Ο διαγωνισμός δεν είναι συνδεδεμένος ούτε με αίτηση ούτε με έργο.');
        }
    }

    public function get_aitisi() {
        return $this->_aitisi;
    }

    public function set_aitisi($_aitisi) {
        $this->_aitisi = $_aitisi;
    }

    public function get_committees() {
        return $this->_committees;
    }

    public function set_committees($_committees) {
        $this->_committees = $_committees;
    }

    public function get_competitiontype() {
        return $this->_competitiontype;
    }

    public function set_competitiontype($_competitiontype) {
        if($this->_competitiontype != $_competitiontype) {
            if($_competitiontype == 1) { // Ανάθεση
                $this->set_refnumnotice(null);
                $this->set_noticedate(null);
                $this->set_execdate(null);
                $this->set_refnumaward(null);
                $this->set_awarddate(null);
            } else if($_competitiontype == 2 || $_competitiontype == 3 || $_competitiontype == 4) { // Τα υπόλοιπα είδη διαγωνισμών
                $this->set_refnumassignment(null);
                $this->set_assignmentdate(null);
            } else {
                throw new Exception('Άγνωστος τύπος διαγωνισμού.');
            }
        }
        $this->_competitiontype = $_competitiontype;
    }

    public function get_procurementtype() {
        return $this->_procurementtype;
    }

    public function set_procurementtype($_procurementtype) {
        $this->_procurementtype = $_procurementtype;
    }

    public function get_technicalconsultant() {
        return $this->_technicalconsultant;
    }

    public function set_technicalconsultant($_technicalconsultant) {
        $this->_technicalconsultant = $_technicalconsultant;
    }

    public function get_offerslanguage() {
        return $this->_offerslanguage;
    }

    public function set_offerslanguage($_offerslanguage) {
        $this->_offerslanguage = $_offerslanguage;
    }

    public function get_offerssubmissionlocation() {
        return $this->_offerssubmissionlocation;
    }

    public function set_offerssubmissionlocation($_offerssubmissionlocation) {
        $this->_offerssubmissionlocation = $_offerssubmissionlocation;
    }

    public function get_offersopeningdate() {
        return $this->_offersopeningdate;
    }

    public function set_offersopeningdate($_offersopeningdate) {
        $this->_offersopeningdate = EDateTime::create($_offersopeningdate);
    }

    public function get_execlocation() {
        return $this->_execlocation;
    }

    public function set_execlocation($_execlocation) {
        $this->_execlocation = $_execlocation;
    }

    public function get_execduration() {
        return $this->_execduration;
    }

    public function set_execduration($_execduration) {
        $this->_execduration = $_execduration;
    }

    public function get_paymentmethod() {
        return $this->_paymentmethod;
    }

    public function set_paymentmethod($_paymentmethod) {
        $this->_paymentmethod = $_paymentmethod;
    }

    public function get_responsibleperson() {
        return $this->_responsibleperson;
    }

    public function set_responsibleperson($_responsibleperson) {
        $this->_responsibleperson = $_responsibleperson;
    }

    public function get_refnumassignment() {
        return $this->_refnumassignment;
    }

    public function set_refnumassignment($_refnumassignment) {
        if($_refnumassignment == null || $this->_competitiontype == 1) {
            $this->_refnumassignment = $_refnumassignment;
        }
    }

    public function get_assignmentdate() {
        return $this->_assignmentdate;
    }

    public function set_assignmentdate($_assignmentdate) {
        if($_assignmentdate == null || $this->_competitiontype == 1) {
            $this->_assignmentdate = EDateTime::create($_assignmentdate);
        }
    }

    public function get_refnumnotice() {
        return $this->_refnumnotice;
    }

    public function set_refnumnotice($_refnumnotice) {
        if($_refnumnotice == null || $this->_competitiontype != 1) {
            $this->_refnumnotice = $_refnumnotice;
        }
    }

    public function get_noticedate() {
        return $this->_noticedate;
    }

    public function set_noticedate($_noticedate) {
        if($_noticedate == null || $this->_competitiontype != 1) {
            $this->_noticedate = EDateTime::create($_noticedate);
        }
    }

    public function get_execdate() {
        return $this->_execdate;
    }

    public function set_execdate($_execdate) {
        if($_execdate == null || $this->_competitiontype != 1) {
            $this->_execdate = EDateTime::create($_execdate);
        }
    }

    public function get_refnumaward() {
        return $this->_refnumaward;
    }

    public function set_refnumaward($_refnumaward) {
        if($_refnumaward == null || $this->_competitiontype != 1) {
            $this->_refnumaward = $_refnumaward;
        }
    }

    public function get_awarddate() {
        return $this->_awarddate;
    }

    public function set_awarddate($_awarddate) {
        if($_awarddate == null || $this->_competitiontype != 1) {
            $this->_awarddate = EDateTime::create($_awarddate);
        }
    }

    /**
     * Επιστρέφει την φάση στην οποία είναι ο διαγωνισμός.
     * @return int Το στάδιο του διαγωνισμού αριθμητικά (τι αντιπροσωπεύει κάθε
     * αριθμός εξαρτάται από τον τύπο του διαγωνισμού)
     */
    public function get_competitionstage() {
        $now = new EDateTime('now');
        if($this->get_competitiontype() == 1) {
            if($this->get_assignmentdate() != null && $this->get_assignmentdate() <= $now) {
                return '1.1'; // Έχει ανατεθεί
            } else {
                return '1.0'; // Δεν έχει ανατεθεί
            }
        } else if($this->get_competitiontype() == 2 || $this->get_competitiontype() == 3 || $this->get_competitiontype() == 4) {
            if($this->get_awarddate() != null && $this->get_awarddate() <= $now) {
                return '2.3'; // Κατακύρωσης
            } else if($this->get_execdate() != null && $this->get_execdate() <= $now) {
                return '2.2'; // Διενέργειας
            } else if($this->get_noticedate() != null && $this->get_noticedate() <= $now) {
                return '2.1'; // Προκήρυξης
            } else {
                return '2.0'; // Δεν έχει προκηρυχθεί
            }
        }
    }

    public function hasDates() {
        if($this->get_assignmentdate() != null || $this->get_noticedate() != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function getCompetitionTypes() {
        $i = 1;
        $types = array();
        while(defined(get_called_class().'::COMPETITIONTYPE_'.$i)) {
            $varname = 'COMPETITIONTYPE_'.$i;
            $types[$i] = constant(get_called_class().'::COMPETITIONTYPE_'.$i);
            $i++;
        }
        return $types;
    }

    public static function getProcurementTypes() {
        $i = 1;
        $types = array();
        while(defined(get_called_class().'::PROCUREMENTTYPE_'.$i)) {
            $varname = 'PROCUREMENTTYPE_'.$i;
            $types[$i] = constant(get_called_class().'::PROCUREMENTTYPE_'.$i);
            $i++;
        }
        return $types;
    }

    public function setOwner($owner) {
        if($owner == null || $owner instanceof Erga_Model_SubProject) {
            $this->set_subproject($owner);
        } else if($owner == null || $owner instanceof Aitiseis_Model_OrismosEpitropisDiagonismou) {
            $this->set_aitisi($owner);
        }
    }

    public function __toString() {
        if($this->get_subproject() != null) {
            return $this->get_subproject()->get_subprojecttitle();
        } else if($this->get_aitisi() != null) {
            return $this->get_aitisi()->get_title();
        } else {
            throw new Exception('Ο διαγωνισμός δεν είναι συνδεδεμένος ούτε με αίτηση ούτε με έργο.');
        }
    }
}
?>