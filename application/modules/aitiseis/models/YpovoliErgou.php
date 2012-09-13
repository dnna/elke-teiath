<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Aitiseis_Model_Repositories_Aitiseis") @Table(name="elke_aitiseis.ypovolisergou")
 */
class Aitiseis_Model_YpovoliErgou extends Aitiseis_Model_AitisiBase {
    const type = "Αίτηση Έγκρισης Υποβολής Έργου";
    const formclass = "Aitiseis_Form_YpovoliErgou";
    const template = "D00-AitisiEgkrisisYpovolisErgou";
    protected $_availableActions = array(self::ACTION_EXPORT);
    /**
     * @ManyToOne (targetEntity="Application_Model_User")
     * @JoinColumn (name="supervisoruserid", referencedColumnName="userid")
     * @var Application_Model_User
     */
    protected $_supervisor;
    /**
     * @Column (name="description", type="string")
     */
    protected $_description;
    /**
     * @Column (name="startdate", type="date")
     * @var EDateTime
     */
    protected $_startdate;
    /**
     * @Column (name="enddate", type="date")
     * @var EDateTime
     */
    protected $_enddate;
    /**
     * @Column (name="totalbudget", type="greekfloat")
     */
    protected $_totalbudget;
    /**
     * @Column (name="teibudget", type="greekfloat")
     */
    protected $_teibudget;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency", cascade={"persist"})
     * @JoinColumn (name="funding", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_fundingagency;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency", cascade={"persist"})
     * @JoinColumn (name="cofunding", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_cofundingagency;
    /**
     * @ManyToOne (targetEntity="Application_Model_Lists_Agency", cascade={"persist"})
     * @JoinColumn (name="contractor", referencedColumnName="id")
     * @var Application_Model_Lists_Agency
     */
    protected $_contractor;
    /**
     * @Column (name="nationalparticipation", type="greekpercentage")
     */
    protected $_nationalparticipation;
    /**
     * @Column (name="europeanparticipation", type="greekpercentage")
     */
    protected $_europeanparticipation;
    /**
     * @Column (name="comments", type="string")
     */
    protected $_comments;
    
    protected $__duration;
    
    public function get_supervisor() {
        return $this->_supervisor;
    }

    public function set_supervisor($_supervisor) {
        $this->_supervisor = $_supervisor;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
    }

    public function get_startdate() {
        return $this->_startdate;
    }

    public function set_startdate($_startdate) {
        $this->_startdate = EDateTime::create($_startdate);
    }

    public function get_enddate() {
        return $this->_enddate;
    }

    public function set_enddate($_enddate) {
        $this->_enddate = EDateTime::create($_enddate);
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

    public function get_fundingagency() {
        return $this->_fundingagency;
    }

    public function set_fundingagency($_fundingagency) {
        $this->_fundingagency = $_fundingagency;
    }

    public function get_cofundingagency() {
        return $this->_cofundingagency;
    }

    public function set_cofundingagency($_cofundingagency) {
        $this->_cofundingagency = $_cofundingagency;
    }

    public function get_contractor() {
        return $this->_contractor;
    }

    public function set_contractor($_contractor) {
        $this->_contractor = $_contractor;
    }

    public function get_nationalparticipation() {
        return $this->_nationalparticipation;
    }

    public function set_nationalparticipation($_nationalparticipation) {
        $this->_nationalparticipation = $_nationalparticipation;
    }

    public function get_europeanparticipation() {
        return $this->_europeanparticipation;
    }

    public function set_europeanparticipation($_europeanparticipation) {
        $this->_europeanparticipation = $_europeanparticipation;
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function get__duration() { // Το προεπιλεγμένο div θα χωρίσει το duration σε μήνες
        $days = $this->get_enddate()->diff($this->get_startdate())
                ->format("%a");
        return $days/30;
    }

    protected function updateProject() {
        $vars = $this->toArray(null, true);
        $vars["supervisor"] = array("userid" => $vars["supervisor"]["userid"]); // Για να μην χάνονται τα roles
        if($this->_project->get_iscomplex() != 0) {
            throw new Exception('Το έργο δεν μπορεί να ενημερωθεί γιατί είναι σύνθετο.');
        }
        $this->_project->get_basicdetails()->setOptions($vars);
        $this->_project->get_financialdetails()->setOptions($vars);
        $this->_project->get_position()->setOptions($vars);
        $this->_project->save();
        return $this->_project;
    }

    public function onApproval() {}

    public function onRejection() {
        if(isset($this->_childrenaitiseis) && $this->_childrenaitiseis->count() > 0) {
            throw new Exception('Η αίτηση δεν μπορεί να απορριφθεί γιατί έχουν κατατεθεί άλλες που βασίζονται σε αυτή.');
        }
        if(isset($this->_project)) {
            throw new Exception('Η αίτηση δεν μπορεί να απορριφθεί γιατί έχει συνδεθεί με έργο.');
        }
    }
    
    public function hasOwnTitle() {
        return true;
    }
}
?>