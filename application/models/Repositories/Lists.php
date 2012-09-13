<?php
use Doctrine\ORM\EntityRepository;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_Lists extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Dnna_Model_Object
     */
    public function getList() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('l');
        $qb->from($this->_entityName, 'l');
        return $this->getResult($qb);
    }

    /**
     * @return Array
     */
    public static function getListAsArray($classname) {
        $ec = Zend_Registry::get('entityManager')->getRepository($classname)->getList();
        $ecArray = Array();
        foreach($ec as $curEc) {
            $ecArray[$curEc->get_id()] = $curEc->get_name();
        }
        return $ecArray;
    }

    /**
     * @return Application_Model_Lists_Agency
     */
    public function findAgencyByName($name, $limit = 10) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('Application_Model_Lists_Agency', 'e');

        $qb->andWhere('e._name LIKE :name');
        $qb->setParameter('name', '%'.$name.'%');

        // TODO δεν χρησιμοποιείται το $limit

        return $this->getResult($qb);
    }

    /**
     * @return Application_Model_Lists_Agency
     */
    public function findAgencyByAfm($afm, $limit = 10) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a');
        $qb->from('Application_Model_Lists_Agency', 'a');

        $qb->andWhere('a._afm LIKE :afm');
        $qb->setParameter('afm', '%'.$afm.'%');

        // TODO δεν χρησιμοποιείται το $limit

        return $this->getResult($qb);
    }
}
?>