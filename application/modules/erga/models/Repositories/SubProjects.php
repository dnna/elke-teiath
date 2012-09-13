<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_SubProjects extends Application_Model_Repositories_BaseRepository
{
    /**
     * @return Erga_Model_Project
     */
    public function findSubprojects($filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from('Erga_Model_SubProject', 'p');

        // Βασικά φίλτρα
        if(isset($filters['projectid'])) {
            $qb->join('p._parentproject', 'pp');
            $qb->andWhere('pp._projectid = '.$filters['projectid']);
        }
        // SubProjectId
        if(isset($filters['subprojectid'])) {
            $qb->andWhere('p._subprojectid = '.$filters['subprojectid']);
        }
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->join('p._subprojectsupervisor', 'sv');
            $qb->andWhere('sv._userid = \''.$filters['supervisoruserid'].'\'');
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'p');
        } else {
            $qb->orderBy('p._subprojecttitle', 'ASC');
        }
        return $this->getResult($qb);
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->join('p._subprojectsupervisor', 's');
        $qb->andWhere('(p._subprojecttitle LIKE :searchterms OR p._subprojecttitleen LIKE :searchterms OR s._realname LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }
}
?>