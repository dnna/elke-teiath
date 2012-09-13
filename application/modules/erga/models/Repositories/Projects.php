<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_Projects extends Application_Model_Repositories_BaseRepository
{
    protected $basicdetailsjoined = false;

    /**
     * @return Erga_Model_Project
     */
    public function findProjects($filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from('Erga_Model_Project', 'p');

        // Βασικά φίλτρα
        // ProjectId
        if(isset($filters['projectid'])) {
            $qb->andWhere('p._projectid = '.$filters['projectid']);
        }
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $this->joinBasicDetails($qb);
            $qb->join('bd._supervisor', 'sv');
            $qb->andWhere('sv._userid = \''.$filters['supervisoruserid'].'\'');
        }
        // MIS
        if(isset($filters['mis'])) {
            $this->joinBasicDetails($qb);
            $qb->andWhere('bd._mis = \''.$filters['mis'].'\'');
        }
        // Φίλτρο ολοκληρωμένων
        if(isset($filters['showcompletes']) && $filters['showcompletes'] != "") {
            $this->addCompletesFilter($qb, $filters['showcompletes']);
        }
        // Εμφάνιση έργων με εκπρόθεσμα παραδοτέα
        if(isset($filters['showoverdues']) && $filters['showoverdues'] != "") {
            $this->addOverduesFilter($qb, $filters['showoverdues']);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            if($sort === 'status') {
                $qb->orderBy('p._iscomplete, p._hasoverduedeliverables', $order);
            } else {
                $this->createOrderByQuery($qb, $sort, $order, 'p');
            }
        } else {
            $qb->orderBy('p._iscomplete, p._hasoverduedeliverables, p._creationdate', 'ASC');
        }
        return $this->getResult($qb);
    }

    protected function addCompletesFilter(Doctrine\ORM\QueryBuilder &$qb, $showcompletes = 'true') {
        $completes = 'SELECT pp._projectid FROM Erga_Model_Project pp LEFT JOIN pp._subprojects spp LEFT JOIN spp._workpackages wpp LEFT JOIN wpp._deliverables dd
                                    WHERE dd._completionapprovaldate IS NOT NULL';
        $incompletes = 'SELECT ppi._projectid FROM Erga_Model_Project ppi LEFT JOIN ppi._subprojects sppi LEFT JOIN sppi._workpackages wppi LEFT JOIN wppi._deliverables ddi
                                    WHERE ddi._completionapprovaldate IS NULL OR ddi IS NULL';
        if($showcompletes === 'true') {
            $qb->andWhere('p._projectid IN ('.$completes.') AND p._projectid NOT IN('.$incompletes.')');
        } else if($showcompletes === 'false') {
            $qb->andWhere('p._projectid IN ('.$incompletes.') OR p._projectid NOT IN ('.$completes.')');
        }
        return $qb;
    }

    protected function addOverduesFilter(Doctrine\ORM\QueryBuilder &$qb, $showoverdues = 'true') {
        $overdues = 'SELECT ppp._projectid FROM Erga_Model_Project ppp LEFT JOIN ppp._subprojects sppp LEFT JOIN sppp._workpackages wppp LEFT JOIN wppp._deliverables ddd
                                    WHERE ddd._completionapprovaldate IS NULL AND ddd._enddate < CURRENT_DATE()';
        if($showoverdues === 'true') {
            $qb->andWhere('p._projectid IN ('.$overdues.')');
        } else if($showoverdues === 'false') {
            $qb->andWhere('p._projectid NOT IN ('.$overdues.')');
        }
        return $qb;
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $this->joinBasicDetails($qb);
        $qb->join('bd._supervisor', 's');
        $qb->andWhere('(bd._mis LIKE :searchterms OR bd._acccode LIKE :searchterms OR bd._title LIKE :searchterms OR bd._titleen LIKE :searchterms OR s._realname LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }
    
    protected function joinBasicDetails(Doctrine\ORM\QueryBuilder &$qb) {
        if($this->basicdetailsjoined == false) {
            $qb->join('p._basicdetails', 'bd');
            $this->basicdetailsjoined = true;
        }
    }
}
?>