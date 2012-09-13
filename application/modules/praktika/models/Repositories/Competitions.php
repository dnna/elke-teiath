<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_Model_Repositories_Competitions extends Application_Model_Repositories_BaseRepository
{
    public function findCompetitions($filters) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c');
        $qb->from('Praktika_Model_Competition', 'c');
        // Φίλτρο ορφανών (για να μην πετάει exception)
        $qb->andWhere('c._subproject IS NOT NULL OR c._aitisi IS NOT NULL');
        // Φίλτρο τύπου
        if(isset($filters['competitiontype']) && $filters['competitiontype'] != "") {
            $this->addTypeFilter($qb, $filters['competitiontype']);
        }
        // Φίλτρο Ημερομηνιών
        // Start
        if(isset($filters['start']) && $filters['start'] != "") {
            $qb->andWhere('c._assignmentdate > :start OR c._noticedate > :start OR c._execdate > :start OR c._awarddate > :start');
            $start = new EDateTime();
            $start->setTimestamp($filters['start']);
            $qb->setParameter('start', $start);
        }
        // End
        if(isset($filters['end']) && $filters['end'] != "") {
            $qb->andWhere('c._assignmentdate < :end OR c._noticedate < :end OR c._execdate < :end OR c._awarddate < :end');
            $end = new EDateTime();
            $end->setTimestamp($filters['end']);
            $qb->setParameter('end', $end);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'c');
        }

        return $this->getResult($qb);
    }

    public function findCompetitionEvents($filters) {
        $competitions = $this->findCompetitions($filters);
        $competitionevents = array();
        foreach($competitions as $curCompetition) {
            /* @var $curCompetition Praktika_Model_Competition */
            if($curCompetition->hasDates()) {
                $curEvent = new Synedriaseisee_Model_CompetitionEvent($curCompetition->get_competitionstage(), $curCompetition);
                $competitionevents[] = $curEvent;
            }
        }
        return $competitionevents;
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->join('c._subproject', 'sp');
        $qb->andWhere('(sp._subprojecttitle LIKE :searchterms OR sp._subprojecttitleen LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }

    protected function addTypeFilter(Doctrine\ORM\QueryBuilder &$qb, $type = "") {
        $qb->andWhere('c._competitiontype = :type');
        $qb->setParameter('type', $type);
    }
}
?>