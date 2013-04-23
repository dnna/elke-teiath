<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_Employees extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Application_Model_Employee
     */
    public function findEmployees(array $filters) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('Application_Model_Employee', 'e');

        if(isset($filters['surname']) && $filters['surname'] != "") {
            $this->addSurnameFilter($qb, $filters['surname']);
        }
        if(isset($filters['afm']) && $filters['afm'] != "") {
            $this->addAfmFilter($qb, $filters['afm']);
        }
        if(isset($filters['ldapNotNull']) && $filters['ldapNotNull'] == true) {
            $qb->andWhere("e._ldapusername IS NOT NULL AND e._ldapusername != ''");
        }

        return $this->getResult($qb);
    }

    protected function addSurnameFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('e._surname LIKE :surname');
        $qb->setParameter('surname', '%'.$searchterms.'%');
    }

    protected function addAfmFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('e._afm LIKE :afm');
        $qb->setParameter('afm', '%'.$searchterms.'%');
    }

    public function garbageCollection() {
        // Σβήνει τις ορφανές εγγραφές απασχολούμενων
        // TODO ίσως σε νεότερες εκδόσεις του Doctrine να μπορεί να γίνει χωρίς το native SQL query
        if($this->_em->getConnection()->getDriver()->getName() !== "pdo_mysql") {
            throw new Exception('Garbage collection απασχολούμενων μπορεί να γίνει μόνο σε MySQL.');
        }
        $this->_em->flush(); // Για να μην υπάρξει κανένα πρόβλημα με αλλαγές που δεν περάστηκαν
        $sql = "DELETE e FROM `employees` e
                LEFT JOIN (SELECT afm, subprojectid, projectid FROM elke_erga.employees UNION SELECT afm, aitisiid as subprojectid, aitisiid as projectid FROM elke_aitiseis.oka_employees) se ON e.afm = se.afm
                WHERE se.subprojectid IS NULL AND se.projectid IS NULL;";
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
?>