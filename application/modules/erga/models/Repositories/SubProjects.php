<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_SubProjects extends Application_Model_Repositories_BaseRepository
{
    /**
     * @return Erga_Model_SubProject
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
        // Φίλτρο ολοκληρωμένων
        if(isset($filters['showcompletes']) && $filters['showcompletes'] != "") {
            $this->addCompletesFilter($qb, $filters['showcompletes']);
        }
        // Εμφάνιση έργων με εκπρόθεσμα παραδοτέα
        if(isset($filters['showoverdues']) && $filters['showoverdues'] != "") {
            $this->addOverduesFilter($qb, $filters['showoverdues']);
        }
        // Από
        if(isset($filters['from']) && $filters['from'] != "") {
            $fromdate = \EDateTime::create($filters['from']);
            $qb->andWhere('p._subprojectstartdate > :fromdate');
            $qb->setParameter('fromdate', $fromdate);
        }
        // Έως
        if(isset($filters['to']) && $filters['to'] != "") {
            $todate = \EDateTime::create($filters['to']);
            $qb->andWhere('p._subprojectenddate < :todate');
            $qb->setParameter('todate', $todate);
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
        // Όχι επιστημονικά υπεύθυνος του project
        if(isset($filters['notprojectsupervisor'])) {
            $qb->join('p._parentproject', 'nsvpp');
            $qb->join('nsvpp._basicdetails', 'nsvppbd');
            $qb->join('nsvppbd._supervisor', 'nsvppbdu');
            $qb->andWhere('nsvppbdu._userid = \''.$filters['notprojectsupervisor'].'\'');
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

    protected function addCompletesFilter(Doctrine\ORM\QueryBuilder &$qb, $showcompletes = 'true') {
        $completes = 'SELECT spp._subprojectid FROM Erga_Model_SubProject spp LEFT JOIN spp._workpackages wpp LEFT JOIN wpp._deliverables dd
                                    WHERE dd._completionapprovaldate IS NOT NULL';
        $incompletes = 'SELECT sppi._subprojectid FROM Erga_Model_SubProject sppi LEFT JOIN sppi._workpackages wppi LEFT JOIN wppi._deliverables ddi
                                    WHERE ddi._completionapprovaldate IS NULL OR ddi IS NULL';
        if($showcompletes === 'true') {
            $qb->andWhere('p._subprojectid IN ('.$completes.') AND p._subprojectid NOT IN('.$incompletes.')');
        } else if($showcompletes === 'false') {
            $qb->andWhere('p._subprojectid IN ('.$incompletes.') OR p._subprojectid NOT IN ('.$completes.')');
        }
        return $qb;
    }

    protected function addOverduesFilter(Doctrine\ORM\QueryBuilder &$qb, $showoverdues = 'true') {
        $overdues = 'SELECT sppp._subprojectid FROM Erga_Model_SubProject sppp LEFT JOIN sppp._workpackages wppp LEFT JOIN wppp._deliverables ddd
                                    WHERE ddd._completionapprovaldate IS NULL AND ddd._enddate < CURRENT_DATE()';
        if($showoverdues === 'true') {
            $qb->andWhere('p._subprojectid IN ('.$overdues.')');
        } else if($showoverdues === 'false') {
            $qb->andWhere('p._subprojectid NOT IN ('.$overdues.')');
        }
        return $qb;
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->join('p._subprojectsupervisor', 's');
        $qb->andWhere('(p._subprojecttitle LIKE :searchterms OR p._subprojecttitleen LIKE :searchterms OR s._realname LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }
}
?>