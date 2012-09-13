<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Model_Repositories_AitisiEmployees extends Application_Model_Repositories_BaseRepository
{
    /**
     * Επιστρέφει πίνακα με συνολικά στοιχεία για κάθε απασχολούμενο. Το
     * αποτέλεσμα έχει την εξής μορφή:
     * Το index 0 έχει τον απασχολούμενο (σαν αντικείμενο)
     * Το index 'projectscount' έχει τον αριθμό των έργων στα οποία έχει
     * καταχωρηθεί ο απασχολούμενος
     * Το index 'totalamount' έχει την συνολική αμοιβή του απασχολούμενου από
     * όλα τα υποέργα (ειναι Αμερικάνικο float, οπότε ίσως θέλει conversion)
     * @return array Τα προαναφερόμενα
     */
    public function getEmployeesAggregate($filters = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e, count(DISTINCT p) as projectscount, sum(e._amount) as totalamount');
        $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'e');
        $qb->innerJoin('e._employee', 'a');
        $qb->innerJoin('e._subproject', 'sp');
        $qb->innerJoin('sp._parentproject', 'p');
        $qb->groupBy('a._afm');
        if(isset($filters['employee']) && $filters['employee'] instanceof Application_Model_Employee) {
            $qb->andWhere('a._afm = \''.$filters['employee']->get_afm().'\'');
        }
        // Φίλτρο με βάση τον επιστημονικά υπεύθυνο των έργων
        if(isset($filters['supervisoruserid'])) {
            $this->addSupervisorFilter($qb, $filters['supervisoruserid']);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
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
            $qb->orderBy('a._surname', 'ASC');
        }

        return $this->getResult($qb);
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('(a._firstname LIKE :searchterms OR a._surname LIKE :searchterms OR a._afm LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }
    
    protected function addSupervisorFilter(Doctrine\ORM\QueryBuilder &$qb, $supervisoruserid) {
        $qb->join('p._basicdetails', 'bd');
        $qb->join('bd._supervisor', 'supervisor');
        $qb->andWhere('supervisor._userid = :supervisoruserid');
        $qb->setParameter('supervisoruserid', $supervisoruserid);
    }

    public function getOverview(Application_Model_Employee $employee, $filters = array()) {
        $overview = array();
        $subprojectemployee = $this->getEmployeesAggregate(array('employee' => $employee) + $filters);
        $overview['employee'] = $subprojectemployee[0];
        $overview['subprojects'] = $this->getSubProjects($employee, $filters);
        $overview['deliverables'] = $this->getDeliverables($employee, $filters);
        return $overview;
    }

    public function getSubProjects(Application_Model_Employee $employee, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sp');
        $qb->from('Erga_Model_SubProject', 'sp');
        $qb->innerJoin('sp._employees', 'e');
        $qb->innerJoin('e._employee', 'a');

        $qb->andWhere('a._afm = :afm');
        $qb->setParameter('afm', $employee->get_afm());

        // Φίλτρα
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->join('sp._subprojectsupervisor', 'supervisor');
            $qb->andWhere('supervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }

        return $this->getResult($qb);
    }

    public function getDeliverables(Application_Model_Employee $employee, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d');
        $qb->from('Erga_Model_SubItems_Deliverable', 'd');
        $qb->innerJoin('d._authors', 'au');
        $qb->innerJoin('au._employee', 'e');
        $qb->innerJoin('e._employee', 'a');

        $qb->andWhere('a._afm = :afm');
        $qb->setParameter('afm', $employee->get_afm());

        // Φίλτρα
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->join('d._workpackage', 'wp');
            $qb->join('wp._subproject', 'sp');
            $qb->join('sp._subprojectsupervisor', 'supervisor');
            $qb->andWhere('supervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }

        return $this->getResult($qb);
    }
}
?>