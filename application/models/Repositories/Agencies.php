<?php
use Doctrine\ORM\EntityRepository;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_Agencies extends Application_Model_Repositories_Lists {
    /**
     * @return Application_Model_Lists_Agency
     */
    public function findAgencies($filters) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a');
        $qb->from('Application_Model_Lists_Agency', 'a');

        if(isset($filters['name']) && $filters['name'] != "") {
            $this->addNameFilter($qb, $filters['name']);
        }
        if(isset($filters['afm']) && $filters['afm'] != "") {
            $this->addAfmFilter($qb, $filters['afm']);
        }

        return $this->getResult($qb);
    }

    protected function addNameFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('a._name LIKE :name');
        $qb->setParameter('name', '%'.$searchterms.'%');
    }

    protected function addAfmFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('a._afm LIKE :afm');
        $qb->setParameter('afm', '%'.$searchterms.'%');
    }
}
?>