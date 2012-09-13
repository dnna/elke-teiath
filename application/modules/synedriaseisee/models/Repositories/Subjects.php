<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_Model_Repositories_Subjects extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Synedriaseisee_Model_Synedriasi
     */
    public function findSubjects($filters = array()) { // Δεν χρησιμοποιείται το limit εδώ
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a');
        $qb->from($this->_entityName, 'a');
        
        // Φίλτρα
        // Start
        if(isset($filters['synedriasiid'])) {
            $qb->join('a._synedriasi', 'syn');
            $qb->andWhere('syn._id = :id');
            $qb->setParameter('id', $filters['synedriasiid']);
        }
        if(isset($filters['aitisiid'])) {
            $qb->join('a._aitisi', 'aitisi');
            $qb->andWhere('aitisi._aitisiid = :aitisiid');
            $qb->setParameter('aitisiid', $filters['aitisiid']);
        }
        if(isset($filters['num'])) {
            $qb->andWhere('a._num = :num');
            $qb->setParameter('num', $filters['num']);
        }

        // Ordering
/*        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'a');
        } else {
            // Default order
        }*/
        return $this->getResult($qb);
    }
}
?>