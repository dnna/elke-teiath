<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Synedriaseisee_Model_Repositories_Synedriaseis") @Table(name="elke_synedriaseisee.synedriaseis")
 */
class Synedriaseisee_Model_Synedriasi extends Synedriaseisee_Model_Event {
    /**
     * @Column (name="num", type="string")
     */
    protected $_num;
    /**
     * @OneToMany (targetEntity="Synedriaseisee_Model_Subject", mappedBy="_synedriasi", orphanRemoval=true, cascade={"all"})
     * @OrderBy ({"_num" = "ASC"})
     * @var Synedriaseisee_Model_Subject
     */
    protected $_subjects;

    protected $_cssClass = 'synedriasi';

    public function __construct() {
            $this->_start = new EDateTime("now");
            $this->_end = new EDateTime("now");
    }

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_num() {
        return $this->_num;
    }

    public function set_num($_num) {
        $this->_num = $_num;
    }

    public function get_title() {
        return 'Συνεδρίαση: '.$this->get_num().'-'.$this->get_start();
    }

    public function get_subjects() {
        return $this->_subjects;
    }

    public function set_subjects($_subjects) {
        $this->_subjects = $_subjects;
    }

    public function findSubject($num) {
        foreach($this->get_subjects() as $curSubject) {
            if($curSubject->get_num() == $num) {
                return $curSubject;
            }
        }
        return null;
    }

    // Κατανέμει τα θέματα στις κατάλληλες κλάσεις ανάλογα με το αν υπάρχει ή
    // όχι aitisiid
    public function setOptions(array $options, $ignoreisvisible = false) {
        if(isset($options['default']) && count($options['default']) > 0) {
            $options = array_merge($options, $options['default']);
            unset($options['default']);
        }
        if(isset($options['subjects'])) {
            foreach($options['subjects'] as &$curSubject) {
                if(!isset($curSubject['aitisi']['aitisiid']) || 
                        $curSubject['aitisi']['aitisiid'] == '' || 
                        $curSubject['aitisi']['aitisiid'] === 'null') {
                    $curSubject['classname'] = 'Synedriaseisee_Model_SimpleSubject';
                } else {
                    $curSubject['classname'] = 'Synedriaseisee_Model_AitisiSubject';
                }
            }
        }
        return parent::setOptions($options, $ignoreisvisible);
    }
}
?>