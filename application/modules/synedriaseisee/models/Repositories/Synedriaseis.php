<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Synedriaseisee_Model_Repositories_Synedriaseis extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Synedriaseisee_Model_Synedriasi
     */
    public function findSynedriaseis($filters = array(), $limit = 0) { // Δεν χρησιμοποιείται το limit εδώ
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a');
        $qb->from($this->_entityName, 'a');
        $qb->orderBy('a._start', 'ASC');
        
        // Φίλτρα
        // Start
        if(isset($filters['start'])) {
            $qb->andWhere('a._start > :start');
            $start = new EDateTime();
            $start->setTimestamp($filters['start']);
            $qb->setParameter('start', $start);
        }
        // End
        if(isset($filters['end'])) {
            $qb->andWhere('a._end < :end');
            $end = new EDateTime();
            $end->setTimestamp($filters['end']);
            $qb->setParameter('end', $end);
        }

        // Ordering
/*        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'a');
        } else {
            // Default order
        }*/
        // Limit
        if($limit > 0) {
            $qb->setMaxResults($limit);
        }
        return $this->getResult($qb);
    }
}
?>