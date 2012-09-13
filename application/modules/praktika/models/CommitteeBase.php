<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Committees") @Table(name="elke_praktika.committee")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({"base" = "Praktika_Model_CommitteeBase", "paralavis" = "Praktika_Model_Committee_Paralavis", "diagonismou" = "Praktika_Model_Committee_Diagonismou", "enstaseon" = "Praktika_Model_Committee_Enstaseon"})
 */
abstract class Praktika_Model_CommitteeBase extends Dnna_Model_Object {
    const type = "Undefined_Type";
    /**
     * @Id
     * @Column (name="id", type="integer")
     * @GeneratedValue
     */
    protected $_id;
    /**
     * @Column (name="active", type="boolean")
     */
    protected $_active = false;
    /**
     * @Column (name="comments", type="string")
     */
    protected $_comments;

    /**
     * @OneToMany (targetEntity="Praktika_Model_Committee_Member", mappedBy="_committee", orphanRemoval=true, cascade={"all"})
     * @var Praktika_Model_Committee_Member
     */
    protected $_committeemembers; // Μέλη της επιτροπής
    /**
     * @OneToMany (targetEntity="Praktika_Model_PraktikoBase", mappedBy="_committee", orphanRemoval=true, cascade={"all"})
     * @var Praktika_Model_PraktikoBase
     */
    protected $_praktika; // Πρακτικά
    
    protected $activechanged = false;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
    }

    public function get_active() {
        return $this->_active;
    }

    public function set_active($_active) {
        if($this->_active != $_active) {
            $this->activechanged = true;
        }
        $this->_active = $_active;
    }

    public function get_comments() {
        return $this->_comments;
    }

    public function set_comments($_comments) {
        $this->_comments = $_comments;
    }

    public function get_committeemembers() {
        return $this->_committeemembers;
    }

    public function set_committeemembers($_committeemembers) {
        $this->_committeemembers = $_committeemembers;
    }

    public function getMembersText() {
        return implode(', ', $this->get_committeemembers()->toArray());
    }

    public function get_praktika() {
        return $this->_praktika;
    }

    public function set_praktika($_praktika) {
        $this->_praktika = $_praktika;
    }

    public function get_project() {
        return null;
    }

    public function get_aitisi() {
        return null;
    }

    public static function getEpitropesTypes() {
        /* @var $metadata Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = Zend_Registry::get('entityManager')->getMetadataFactory()->getMetadataFor(__CLASS__);
        $discriminatormap = $metadata->discriminatorMap;
        unset($discriminatormap['base']);
        return $discriminatormap;
    }

    public static function getEpitropesTypesText() {
        $epitropestypes = self::getEpitropesTypes();
        $mappings = array();
        foreach($epitropestypes as $curMapping => $curClass) {
            $mappings[$curMapping] = $curClass::type;
        }
        return $mappings;
    }

    public static function getEpitropiMapping($input) {
        $epitropestypes = self::getEpitropesTypes();
        return @$epitropestypes[$input];
    }

    public static function getEpitropiMappingText($input) {
        $epitropestypes = self::getEpitropesTypesText();
        return @$epitropestypes[$input];
    }

    public static function getReverseMapping($classname) {
        foreach(self::getEpitropesTypes() as $curMapping => $curClass) {
            if($classname === $curClass) {
                return $curMapping;
            }
        }
        throw new Exception('Δεν βρέθηκε το reverse mapping');
    }

    public static function factory($type, $options = null) {
        $classname = self::getEpitropiMapping($type);
        return new $classname($options);
    }
}
?>