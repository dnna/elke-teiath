<?php
use Doctrine\ORM\EntityRepository;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_EmployeeLists extends Application_Model_Repositories_Lists {
    /**
     * @return Dnna_Model_Object
     */
    public function getList() {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('l, LENGTH(l._id) as idlength');
        $qb->from($this->_entityName, 'l');
        $qb->orderBy('idlength, l._id', 'ASC');
        $result = $this->getResult($qb);
        foreach($result as &$curResult) {
            $curResult = $curResult[0];
        }
        return $result;
    }

    /**
     * @return Array
     */
    public static function getListAsArray($classname) {
        $ec = Zend_Registry::get('entityManager')->getRepository($classname)->getList();
        $ecArray = Array();
        foreach($ec as $curEc) {
            $ecArray[$curEc->get_id()] = $curEc->get_id().': '.$curEc->get_name();
        }
        return $ecArray;
    }
}
?>