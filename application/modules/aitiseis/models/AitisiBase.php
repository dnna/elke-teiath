<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.aitiseis") @HasLifecycleCallbacks
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="aitisitype", type="string")
 * @DiscriminatorMap({
 * "aitisibase" = "Aitiseis_Model_AitisiBase",
 * "ypovoliaitimatos" = "Aitiseis_Model_YpovoliAitimatos",
 * "ypovoliergou" = "Aitiseis_Model_YpovoliErgou",
 * "oikonomikidiaxeirisiergou" = "Aitiseis_Model_OikonomikiDiaxeirisi_Ergou",
 * "onomastikikatastasi" = "Aitiseis_Model_OnomastikiKatastasi",
 * "daneismou" = "Aitiseis_Model_Daneismou",
 * "dhmiourgiaepitropisparalavis" = "Aitiseis_Model_DhmiourgiaEpitropisParalavis",
 * "telikouapologismou" = "Aitiseis_Model_TelikosApologismos",
 * "orismosepitropisdiagonismou" = "Aitiseis_Model_OrismosEpitropisDiagonismou",
 * "entolipliromis" = "Aitiseis_Model_EntoliPliromis",
 * })
 */
// * "oikonomikidiaxeirisisynedriou" = "Aitiseis_Model_OikonomikiDiaxeirisi_Synedriou",
// * "anatheorisiproypologismou" = "Aitiseis_Model_AnatheorisiProypologismou",
abstract class Aitiseis_Model_AitisiBase extends Dnna_Model_Object {
    const type = "Undefined_Type";
    const formclass = "Undefined_Form";
    const template = "Undefined_Template";
    /**
     * @Id
     * @Column (name="aitisiid", type="integer")
     * @GeneratedValue
     */
    protected $_aitisiid;

    protected $_aitisiname;
    /**
     * @ManyToOne (targetEntity="Application_Model_User")
     * @JoinColumn (name="creatorid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_creator;

    protected $_shorttype;

    const PENDING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    /**
     * @Column (name="approved", type="integer")
     */
    protected $_approved = self::PENDING;

    const ACTION_EXPORT = 0;
    const ACTION_DOWNLOAD = 1;
    protected $_availableActions = array();

    protected $_approvedtext;
    /**
     * @ManyToOne (targetEntity="Synedriaseisee_Model_Synedriasi")
     * @JoinColumn (name="sessionid", referencedColumnName="id")
     * @var Synedriaseisee_Model_Synedriasi
     */
    protected $_session;
    /**
     * @ManyToOne (targetEntity="Synedriaseisee_Model_AitisiSubject", cascade={"all"})
     * @JoinColumn (name="sessionsubjectid", referencedColumnName="recordid")
     * @var Synedriaseisee_Model_AitisiSubject
     */
    protected $_sessionsubject;
    /**
     * @Column (name="creationdate", type="datetime")
     * @var EDateTime
     */
    protected $_creationdate;
    /**
     * @Column (name="lastupdatedate", type="datetime")
     * @var EDateTime
     */
    protected $_lastupdatedate;
    /**
     * @ManyToOne (targetEntity="Aitiseis_Model_AitisiBase", inversedBy="_childrenaitiseis")
     * @JoinColumn (name="parentaitisiid", referencedColumnName="aitisiid")
     * @var Aitiseis_Model_AitisiBase
     */
    protected $_parentaitisi;
    /**
     * @ManyToOne (targetEntity="Erga_Model_Project", inversedBy="_aitiseis")
     * @JoinColumn (name="projectid", referencedColumnName="projectid")
     * @var Erga_Model_Project
     */
    protected $_project;
    /**
     * @Column (name="title", type="string")
     */
    protected $_title;
    /**
     * @Column (name="titleen", type="string")
     */
    protected $_titleen; // Ο τίτλος στα Αγγλικά
    /**
     * @OneToMany (targetEntity="Aitiseis_Model_AitisiBase", mappedBy="_parentaitisi")
     * @var Aitiseis_Model_AitisiBase
     */
    protected $_childrenaitiseis;
    /**
     * @OneToMany (targetEntity="Synedriaseisee_Model_AitisiSubject", mappedBy="_aitisi")
     * @var Synedriaseisee_Model_AitisiSubject
     */
    protected $_subjects; // Θέματα συνεδριάσεων στα οποία θα συζητηθεί αυτή η αίτηση

    private $approvedchanged = false;

    private $sessionchanged = false;

    public function __construct(array $options = null) {
        parent::__construct($options);
        $auth = Zend_Auth::getInstance();
        $this->set_creator(Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->find($auth->getStorage()->read()->get_userid()));
        if(!isset($this->_creationdate)) {
            $this->_creationdate = new EDateTime("now");
        }
    }

    /**
     * @postPersist
     * @postUpdate
     */
    public function postUpdate() {
        if($this->sessionchanged) {
            // Το νέο session είναι διαφορετικό από το παλιό
            $emailaitisi = Zend_Controller_Action_HelperBroker::getStaticHelper('EmailAitisi');
            $emailaitisi->direct($this, 'changesession');
        }
        if($this->approvedchanged) {
            $emailaitisi = Zend_Controller_Action_HelperBroker::getStaticHelper('EmailAitisi');
            $emailaitisi->direct($this, 'changeapproval');
        }
    }

    /**
     * @postPersist
     */
    public function postPersist() {
        $emailaitisi = Zend_Controller_Action_HelperBroker::getStaticHelper('EmailAitisi');
        $emailaitisi->direct($this, 'new');
    }

    /**
     * @postLoad
     * @prePersist
     */
    public function getDataFromParent() {
        $parentaitisi = $this->get_parentaitisi();
        $project = $this->get_project();
        if($this->hasOwnTitle() != true && $parentaitisi != null) {
            $this->set_title($parentaitisi->get_title());
        } else if($this->hasOwnTitle() != true && $project != null) {
            $this->set_title($project->get_basicdetails()->get_title());
        } else if($this->hasOwnTitle() != true) {
            //throw new Exception('Δεν μπόρεσε να βρεθεί ο τίτλος της αίτησης.'); // Προκαλεί bug στο pre-save αφού δεν έχει οριστεί project
        }
    }

    public static function getAitiseisTypes() {
        /* @var $metadata Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = Zend_Registry::get('entityManager')->getMetadataFactory()->getMetadataFor(get_called_class());
        $discriminatormap = $metadata->discriminatorMap;
        unset($discriminatormap['aitisibase']);
        return $discriminatormap;
    }

    public function get_aitisiid() {
        return $this->_aitisiid;
    }

    public function set_aitisiid($_aitisiid) {
        $this->_aitisiid = $_aitisiid;
    }

    public function get_aitisiname() {
        return $this->__toString();
    }

    public function get_creator() {
        return $this->_creator;
    }

    public function set_creator($_creator) {
        $this->_creator = $_creator;
    }

    public function get_shorttype() {
        $front = Zend_Controller_Front::getInstance();
        $aitiseismoduledir = $front->getModuleDirectory('aitiseis');
        require_once($aitiseismoduledir.'/controllers/helpers/GetReverseMapping.php');
        $mappinghelper = new Aitiseis_Action_Helper_GetReverseMapping();
        $this->_shorttype = $mappinghelper->direct($this->get__classname());
        return $this->_shorttype;
    }

    public function get_approved() {
        return $this->_approved;
    }

    public function set_approved($_approved) {
        $oldapproved = $this->get_approved();
        if($this->_approved != $_approved) {
            // Το νέο approved είναι διαφορετικό από το παλιό
            if($_approved == Aitiseis_Model_AitisiBase::APPROVED) {
                $this->onApproval();
            } else if($_approved == Aitiseis_Model_AitisiBase::REJECTED) {
                $this->onRejection();
            }
        }
        $this->_approved = $_approved;
        if($oldapproved != $_approved) { // Δεν μπαίνει στο πάνω if γιατί πρέπει να έχει οριστεί το $this->_approved
            $this->approvedchanged = true;
        }
    }

    public function get_approvedtext() {
        if($this->_approved == Aitiseis_Model_AitisiBase::APPROVED) {
            $this->_approvedtext = 'Εγκρίθηκε '.$this->get_session();
        } else if($this->_approved == Aitiseis_Model_AitisiBase::REJECTED) {
            $this->_approvedtext = 'Απορρίφθηκε '.$this->get_session();
        } else {
            $now = new EDateTime('now');
            if($this->get_session() != null) {
                $this->_approvedtext = 'Συνεδρίαση '.$this->get_session();
            } else {
                $this->_approvedtext = 'Εκκρεμεί';
            }
        }
        return $this->_approvedtext;
    }

    public function get_session() {
        return $this->_session;
    }

    public function set_session(Synedriaseisee_Model_Synedriasi $_session = null) {
        $oldsession = $this->get_session();
        $this->_session = $_session;
        if($_session != null && $oldsession != $_session) {
            $this->sessionchanged = true;
        }
    }

    public function get_sessionsubject() {
        return $this->_sessionsubject;
    }

    public function set_sessionsubject(Synedriaseisee_Model_AitisiSubject $_sessionsubject = null) {
        $oldsessionsubject = $this->get_sessionsubject();
        if($_sessionsubject != null && $oldsessionsubject != null && $_sessionsubject !== $oldsessionsubject) {
            $this->_sessionsubject = null;
            $oldsessionsubject->remove();
            $this->set_session($_sessionsubject->get_synedriasi()); // Το σβήνει στο onRemove του προηγούμενου subject
        }
        $this->_sessionsubject = $_sessionsubject;
    }

    public function get_creationdate() {
        return $this->_creationdate;
    }

    public function set_creationdate($_creationdate) {
        $this->_creationdate = EDateTime::create($_creationdate);
    }

    public function get_lastupdatedate() {
        if(isset($this->_lastupdatedate)) {
            return $this->_lastupdatedate;
        } else {
            return $this->get_creationdate();
        }
    }

    public function get_parentaitisi() {
        return $this->_parentaitisi;
    }

    public function set_parentaitisi($_parentaitisi) {
        $this->_parentaitisi = $_parentaitisi;
    }

    public function get_project() {
        return $this->_project;
    }

    public function set_project(Erga_Model_Project $_project = null, $recursive = true) {
        if($recursive == true) {
            $parentaitisi = $this->get_parentaitisi();
            if($parentaitisi != null) {
                $parentaitisi->set_project($_project);
            }
        }
        $this->_project = $_project;
    }

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
    }

    public function get_titleen() {
        return $this->_titleen;
    }

    public function set_titleen($_titleen) {
        $this->_titleen = $_titleen;
    }

    public function get_childrenaitiseis() {
        return $this->_childrenaitiseis;
    }

    public function set_childrenaitiseis($_childrenaitiseis) {
        $this->_childrenaitiseis = $_childrenaitiseis;
    }

    public function get_subjects() {
        return $this->_subjects;
    }

    public function set_subjects($_subjects) {
        $this->_subjects = $_subjects;
    }

    public function exportToProject() {
        if($this->get_project() == null) {
            $newproject = new Erga_Model_Project();
            $newproject->set_iscomplex(0);
            $this->set_project($newproject);
        }
        $parentaitisi = $this->get_parentaitisi();
        if($parentaitisi != null) {
            $parentaitisi->set_project($this->get_project()); // Ορισμός του projectid σε όλες τις πατρικές αιτήσεις
            $parentaitisi->exportToProject();
        }
        $this->updateProject();
        return $this->get_project();
    }

    // Δημιουργεί τα subjects με βάση το num τους αντί να ακολουθεί το default pattern
    public function setOptions(array $options, $ignoreisvisible = false) {
        if(isset($options['default']) && count($options['default']) > 0) {
            $options = array_merge($options, $options['default']);
            unset($options['default']);
        }
        if(isset($options['sessionsubject'])) {
            $oldsessionsubject = $this->get_sessionsubject();
            $em = Zend_Registry::get('entityManager');
            $synedriasi = $em->getRepository('Synedriaseisee_Model_Synedriasi')->find($options['session']['id']);
            $subject = $em->getRepository('Synedriaseisee_Model_AitisiSubject')->findSubjects(array(
                'synedriasiid' => $options['session']['id'],
                'num' => $options['sessionsubject']['num']));
            if(count($subject) > 0) {
                $subject = $subject[0];
            } else {
                $subject = new Synedriaseisee_Model_AitisiSubject();
            }
            $subject->set_aitisi($this);
            $subject->set_synedriasi($synedriasi);
            $subject->setOptions($options['sessionsubject']);
            $em->persist($subject);
            $this->set_sessionsubject($subject);
            unset($options['sessionsubject']);

            // Αν το παλιό και το νέο αφορούν την ίδια συνεδρίαση τότε μάλλον το παλιό num ήταν λάθος και έτσι σβήνουμε το παλιό
            /*if(($oldsessionsubject->get_synedriasi() === $this->get_sessionsubject()->get_synedriasi()) &&
                    ($oldsessionsubject->get_num() != $this->get_sessionsubject()->get_num())) {
                $em->remove($oldsessionsubject);
            } else if($oldsessionsubject !== $this->get_sessionsubject()) {
                // TODO μήνυμα στο flash messenger ότι το παλιό subject δεν σβήστηκε και πλέον υπάρχουν και τα 2
            }*/
            if($oldsessionsubject != null && $oldsessionsubject !== $this->get_sessionsubject()) {
                $em->remove($oldsessionsubject);
            }
            $em->flush();
        }
        return parent::setOptions($options, $ignoreisvisible);
    }

    // Επιστρέφει την πιο πρόσφατη συνεδρίαση στην οποία η συγκεκριμένη
    // αίτηση υπήρξε ως θέμα.
    public function getLastScheduled() {
        $exists = false;
        $now = new EDateTime('now');
        $oldest = new EDateTime();
        foreach($this->get_subjects() as $curSubject) {
            $start = $curSubject->get_synedriasi()->get_start();
            if($start < $now && $start > $oldest) {
                $exists = true;
                $oldest = $start;
            }
        }
        if($exists == true) {
            return $oldest;
        } else {
            return false;
        }
    }

    /**
     * Επιστρέφει την κοντινότερη ημερομηνία συνεδρίασης στην οποία είναι θέμα
     * αυτή η αίτηση.
     */
    public function getNextScheduled() {
        $scheduled = false;
        $now = new EDateTime('now');
        foreach($this->get_subjects() as $curSubject) {
            $start = $curSubject->get_synedriasi()->get_start();
            if($start > $now) {
                $scheduled = true;
                $now = $start;
            }
        }
        if($scheduled == true) {
            return $now;
        } else {
            return false;
        }
    }

    public function __toString() {
        if($this->get_title() != null) {
            return $this->get_title();
        } else {
            return "";
        }
    }

    /**
     * Επιστρέφει το αν αυτή η αίτηση επιτρέπεται να διαγραφεί.
     * @return boolean true αν επιτρέπεται ή false αν δεν επιτρέπεται.
     */
    public function isDeletable() {
        if($this->get_approved() == Aitiseis_Model_AitisiBase::APPROVED ||
           (isset($this->_childrenaitiseis) && $this->_childrenaitiseis->count() > 0)) {
            return false;
        } else {
            return true;
        }
    }

    protected abstract function updateProject();

    /**
     * Επιστρέφει τα δυνατά actions για αυτή την αίτηση. Αυτή τη στιγμή
     * υποστηρίζονται τα ACTION_EXPORT που επιτρέπει τη δημιουργία project
     * από την αίτηση και ACTION_DOWNLOAD που επιτρέπει το κατέβασμα του
     * συνημμένου αρχείου.
     */
    public function getAvailableActions() {
        return $this->_availableActions;
    }

    public function save() {
        $this->_lastupdatedate = new EDateTime("now");
        return parent::save();
    }

    public function get_id() {
        return $this->get_aitisiid();
    }

    /**
     * Εκτελείται όταν η αίτηση εγκρίνεται.
     */
    abstract function onApproval();

    /**
     * Εκτελείται όταν η αίτηση απορρίπτεται.
     */
    abstract function onRejection();

    abstract function hasOwnTitle();
}
?>