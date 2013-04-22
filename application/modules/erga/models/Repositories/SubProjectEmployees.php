<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_SubProjectEmployees extends Application_Model_Repositories_BaseRepository
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
        $qb->select('e, count(DISTINCT p) + count(DISTINCT ep) as projectscount, sum(e._amount) as totalamount');
        $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'e');
        $qb->innerJoin('e._employee', 'a');
        $qb->leftJoin('e._subproject', 'sp');
        $qb->leftJoin('e._project', 'ep');
        $qb->leftJoin('sp._parentproject', 'p');
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
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->andWhere('ep._iscomplete = FALSE OR p._iscomplete = FALSE');
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
        $overview['symvaseis'] = $this->getSymvaseis($employee, $filters);
        $overview['deliverables'] = $this->getDeliverables($employee, $filters);
        return $overview;
    }

    public function getSymvaseis(Application_Model_Employee $employee, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('spe');
        $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'spe');
        $qb->innerJoin('spe._employee', 'a');

        $qb->andWhere('a._afm = :afm');
        $qb->setParameter('afm', $employee->get_afm());

        // Φίλτρα
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $qb->leftJoin('spe._subproject', 'sp');
            $qb->leftJoin('sp._subprojectsupervisor', 'spsupervisor');

            $qb->leftJoin('spe._project', 'p');
            $qb->leftJoin('p._basicdetails', 'bd');
            $qb->leftJoin('bd._supervisor', 'psupervisor');

            $qb->andWhere('spsupervisor._userid = :supervisoruserid OR psupervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->leftJoin('spe._subproject', 'ssps');
            $qb->leftJoin('spe._project', 'seps');
            $qb->leftJoin('ssps._parentproject', 'sps');
            $qb->andWhere('seps._iscomplete = FALSE OR sps._iscomplete = FALSE');
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

        $qb->leftJoin('e._subproject', 'sspd');
        $qb->leftJoin('e._project', 'sepd');
        $qb->leftJoin('sspd._parentproject', 'spd');

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
            $qb->andWhere('sepd._iscomplete = FALSE OR spd._iscomplete = FALSE');
        }
        $qb->orderBy('spd._projectid, sepd._projectid, sspd._subprojectnumber, d._codename', 'ASC');

        return $this->getResult($qb);
    }

    public function findEmployeeByAfm($afm) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'e');
        $qb->join('e._employee', 'ee');
        $qb->andWhere('ee._afm = :afm');
        $qb->setParameter('afm', $afm);
        return $qb->getQuery()->getResult();
    }

    public function findEmployeeByLdapUsername($ldapusername) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'e');
        $qb->join('e._employee', 'ee');
        $qb->andWhere('ee._ldapusername = :ldapusername');
        $qb->setParameter('ldapusername', $ldapusername);
        return $qb->getQuery()->getResult();
    }
}
?>