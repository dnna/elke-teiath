<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_Contractors extends Application_Model_Repositories_BaseRepository {
    /**
     * @return Application_Model_Contractor
     */
    public function findContractors(array $filters) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->from('Application_Model_Contractor', 'e');

        if(isset($filters['name']) && $filters['name'] != "") {
            $this->addNameFilter($qb, $filters['name']);
        }
        if(isset($filters['afm']) && $filters['afm'] != "") {
            $this->addAfmFilter($qb, $filters['afm']);
        }

        return $this->getResult($qb);
    }

    protected function addNameFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('e._name LIKE :name');
        $qb->setParameter('name', '%'.$searchterms.'%');
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
        $sql = "DELETE c FROM `contractors` c
                LEFT JOIN (SELECT * FROM elke_erga.contractors) se ON c.afm = se.contractorafm
                WHERE se.subprojectid IS NULL;";
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
?>