<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_SubProjectContractors extends Application_Model_Repositories_BaseRepository
{
    /**
     * Επιστρέφει πίνακα με συνολικά στοιχεία για κάθε ανάδοχο. Το
     * αποτέλεσμα έχει την εξής μορφή:
     * Το index 0 έχει τον απασχολούμενο (σαν αντικείμενο)
     * Το index 'projectscount' έχει τον αριθμό των έργων στα οποία έχει
     * καταχωρηθεί ο ανάδοχος
     * Το index 'totalamount' έχει την συνολική αμοιβή του αναδόχου από
     * όλα τα υποέργα (ειναι Αμερικάνικο float, οπότε ίσως θέλει conversion)
     * @return array Τα προαναφερόμενα
     */
    public function getContractorsAggregate($filters = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c, count(DISTINCT p) as projectscount, sum(c._amount) as totalamount');
        $qb->from('Erga_Model_SubItems_SubProjectContractor', 'c');
        $qb->innerJoin('c._agency', 'a');
        $qb->innerJoin('c._subproject', 'sp');
        $qb->innerJoin('sp._parentproject', 'p');
        $qb->groupBy('a._afm');
        if(isset($filters['agency']) && $filters['agency'] instanceof Application_Model_Contractor) {
            $qb->andWhere('a._afm = \''.$filters['agency']->get_afm().'\'');
        }
        // Φίλτρο με βάση τον επιστημονικά υπεύθυνο των έργων
        if(isset($filters['supervisoruserid'])) {
            $this->addSupervisorFilter($qb, $filters['supervisoruserid']);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->andWhere('p._iscomplete = FALSE');
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            if($sort === 'projectscount') {
                $qb->orderBy('projectscount', $order);
            } else if($sort === 'totalamount') {
                $qb->orderBy('totalamount', $order);
            } else {
                $this->createOrderByQuery($qb, $sort, $order, 'a');
            }
        } else {
            $qb->orderBy('a._name', 'ASC');
        }

        return $this->getResult($qb);
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('(a._name LIKE :searchterms OR a._afm LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }

    protected function addSupervisorFilter(Doctrine\ORM\QueryBuilder &$qb, $supervisoruserid) {
        $qb->join('p._basicdetails', 'bd');
        $qb->join('bd._supervisor', 'supervisor');
        $qb->andWhere('supervisor._userid = :supervisoruserid');
        $qb->setParameter('supervisoruserid', $supervisoruserid);
    }

    public function getOverview(Application_Model_Contractor $agency, $filters = array()) {
        $overview = array();
        $contractor = $this->getContractorsAggregate(array('agency' => $agency) + $filters);
        $overview['contractor'] = $contractor[0];
        $overview['subprojects'] = $this->getSubProjects($agency, $filters);
        $overview['deliverables'] = $this->getDeliverables($agency, $filters);
        return $overview;
    }

    public function getSubProjects(Application_Model_Contractor $agency, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sp');
        $qb->from('Erga_Model_SubProject', 'sp');
        $qb->innerJoin('sp._contractors', 'c');
        $qb->innerJoin('c._agency', 'a');

        $qb->andWhere('a._afm = :afm');
        $qb->setParameter('afm', $agency->get_afm());

        // Φίλτρα
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->join('sp._subprojectsupervisor', 'supervisor');
            $qb->andWhere('supervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->leftJoin('c._subproject', 'sspcs');
            $qb->leftJoin('sspcs._parentproject', 'spcs');
            $qb->andWhere('spcs._iscomplete = FALSE');
        }

        return $this->getResult($qb);
    }

    public function getDeliverables(Application_Model_Contractor $agency, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d');
        $qb->from('Erga_Model_SubItems_Deliverable', 'd');
        $qb->innerJoin('d._contractor', 'c');
        $qb->innerJoin('c._agency', 'a');

        $qb->andWhere('a._afm = :afm');
        $qb->setParameter('afm', $agency->get_afm());

        // Φίλτρα
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->join('d._workpackage', 'wp');
            $qb->join('wp._subproject', 'sp');
            $qb->join('sp._subprojectsupervisor', 'supervisor');
            $qb->andWhere('supervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->leftJoin('c._subproject', 'sspcd');
            $qb->leftJoin('sspcd._parentproject', 'spcd');
            $qb->andWhere('spcd._iscomplete = FALSE');
        }

        return $this->getResult($qb);
    }
}
?>