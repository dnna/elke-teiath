<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Praktika_Model_Repositories_Praktika") @Table(name="elke_praktika.praktika") @HasLifecycleCallbacks
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="aitisitype", type="string")
 * @DiscriminatorMap({"praktikobase" = "Praktika_Model_PraktikoBase", "praktikoparalavis" = "Praktika_Model_Paralavis"})
 */
abstract class Praktika_Model_PraktikoBase extends Aitiseis_Model_AitisiBase {
    /**
     * @ManyToOne (targetEntity="Praktika_Model_CommitteeBase", inversedBy="_praktika")
     * @JoinColumn (name="committeeid", referencedColumnName="id")
     */
    protected $_committee;

    /**
     * @postPersist
     */
    public function postPersist() {
        //$emailaitisi = Zend_Controller_Action_HelperBroker::getStaticHelper('EmailAitisi');
        //$emailaitisi->direct($this, 'new');
    }

    public function updateProject() {}

    public function onApproval() {}

    public function onRejection() {}

    public function hasOwnTitle() { return true; }

    public static function getPraktikaTypes() {
        /* @var $metadata Doctrine\ORM\Mapping\ClassMetadata */
        $aitiseisbasemetadata = Zend_Registry::get('entityManager')->getMetadataFactory()->getMetadataFor('Aitiseis_Model_AitisiBase');
        $metadata = Zend_Registry::get('entityManager')->getMetadataFactory()->getMetadataFor(__CLASS__);
        $discriminatormap = array_diff($metadata->discriminatorMap, $aitiseisbasemetadata->discriminatorMap);
        unset($discriminatormap['praktikobase']);
        return $discriminatormap;
    }

    public static function getPraktikaTypesText() {
        $aitiseistypes = Praktika_Model_PraktikoBase::getPraktikaTypes();
        $mappings = array();
        foreach($aitiseistypes as $curMapping => $curClass) {
            $mappings[$curMapping] = $curClass::type;
        }
        return $mappings;
    }

    public static function getPraktikoMapping($input) {
        $praktikatypes = self::getPraktikaTypes();
        return @$praktikatypes[$input];
    }

    public static function getPraktikoMappingText($input) {
        $praktikatypes = self::getPraktikaTypesText();
        return @$praktikatypes[$input];
    }
}
?>