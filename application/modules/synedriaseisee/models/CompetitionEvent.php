<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_Model_CompetitionEvent extends Synedriaseisee_Model_Event {
    private $_stages = array('1.0' => 'Ανάθεση',
                              '1.1' => 'Ανάθεση',
                              '2.0' => 'Προκήρηξη',
                              '2.1' => 'Προκήρηξη',
                              '2.2' => 'Διενέργεια',
                              '2.3' => 'Κατακύρωση');
    /**
     * @var Praktika_Model_Competition
     */
    protected $_competition;

    protected $_stage;

    protected $_allDay = true;

    protected $_cssClass = 'compevent';

    public function __construct($stage, $competition) {
        $this->set_stage($stage);
        $this->set_competition($competition);
    }

    public function get_id() {
        if($this->get_competition()->get_subproject() != null) {
            return 'comp'.$this->get_competition()->get_subproject()->get_subprojectid();
        } else {
            return 'compaitisi'.$this->get_competition()->get_aitisi()->get_aitisiid();
        }
    }

    public function get_title() {
        if($this->get_competition()->get_subproject() != null) {
            return $this->_stages[$this->get_stage()].': '.$this->get_competition()->get_subproject()->get_subprojecttitle();
        } else {
            return $this->_stages[$this->get_stage()].': '.$this->get_competition()->get_aitisi()->get_title();
        }
    }

    public function get_competition() {
        return $this->_competition;
    }

    public function set_competition($_competition) {
        $this->_competition = $_competition;
    }

    public function get_stage() {
        return $this->_stage;
    }

    public function set_stage($_stage) {
        if(in_array($_stage, array_keys($this->_stages))) {
            $this->_stage = $_stage;
        } else {
            throw new Exception('Λανθασμένο στάδιο διαγωνισμού '.$_stage);
        }
    }

    public function get_start() {
        $stage = $this->get_stage();
        if($stage === '1.0' || $stage === '1.1') {
            $start = $this->get_competition()->get_assignmentdate();
        } else if($stage === '2.0' || $stage === '2.1') {
            $start = $this->get_competition()->get_noticedate();
        } else if($stage === '2.2') {
            $start = $this->get_competition()->get_execdate();
        } else if($stage === '2.3') {
            $start = $this->get_competition()->get_awarddate();
        } else {
            throw new Exception('Σφάλμα κατά την εύρεση του τύπου διαγωνισμού');
        }
        /* @var $start EDateTime */
        $start->setTime(10, 0, 0);
        return $start;
    }

    public function get_end() {
        $end = clone $this->get_start();
        return $end->add(new DateInterval('PT2H'));
    }
}
?>