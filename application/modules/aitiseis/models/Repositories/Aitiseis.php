<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Model_Repositories_Aitiseis extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Aitiseis_Model_AitisiBase
     */
    public function findAitiseis($filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a');
        $qb->from($this->_entityName, 'a');

        // Βασικά φίλτρα
        if(isset($filters['aitisiid'])) {
            $qb->andWhere('a._aitisiid = '.$filters['aitisiid']);
        }
        if(isset($filters['creator'])) {
            $qb->join('a._creator', 'c');
            $qb->andWhere('c._userid = :creatoruserid');
            $qb->setParameter('creatoruserid', $filters['creator']);
        }
        if(isset($filters['approved'])) {
            $qb->andWhere('a._approved = '.$filters['approved']);
        }
        if(isset($filters['sessionid'])) {
            $qb->join('a._session', 's');
            $qb->andWhere('s._id = :fsessionid');
            $qb->setParameter('fsessionid', $filters['sessionid']);
        }
        // Φιλτράρισμα με βάση το αν έχει οριστεί συνεδρίαση
        if(isset($filters['scheduled'])) {
            $this->addScheduledFilter($qb, $filters['scheduled']);
        }
        // Φιλτράρισμα με βάση το αν η ημερομηνία της συνεδρίασης έχει περάσει
        if(isset($filters['sessionpassed'])) {
            $this->addSessionPassedFilter($qb, $filters['sessionpassed']);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'a');
        } else {
            $qb->orderBy('a._creationdate', 'DESC');
            $qb->orderBy('a._approved', 'ASC');
        }
        return $this->getResult($qb);
    }

    protected function addScheduledFilter(Doctrine\ORM\QueryBuilder &$qb, $scheduled = false) {
        $qb->leftJoin('a._subjects', 'subjects');
        if($scheduled == true) {
            $qb->andWhere('subjects._recordid IS NOT NULL');
        } else {
            $qb->andWhere('subjects._recordid IS NULL');
        }
    }
    
    protected function addSessionPassedFilter(Doctrine\ORM\QueryBuilder &$qb, $sessionpassed = false) {
        $qb->join('a._session', 'sesp');
        $qb->andWhere('sesp._end < CURRENT_TIMESTAMP()');
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('a._title LIKE :searchterms');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }
}
?>