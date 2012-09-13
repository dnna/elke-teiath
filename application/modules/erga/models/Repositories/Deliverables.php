<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Model_Repositories_Deliverables extends Application_Model_Repositories_BaseRepository
{
    protected $_authorsjoined = false;
    protected $_contractorjoined = false;
    protected $_spjoined = false;

    public function findDeliverables($filters) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d');
        $qb->from('Erga_Model_SubItems_Deliverable', 'd');

        // Βασικά φίλτρα
        if(isset($filters['projectid'])) {
            $this->joinSp($qb);
            $qb->join('sp._parentproject', 'pp');
            $qb->andWhere('pp._projectid = '.$filters['projectid']);
        }
        // SubProjectId
        if(isset($filters['subprojectid'])) {
            $this->joinSp($qb);
            $qb->andWhere('sp._subprojectid = '.$filters['subprojectid']);
        }
        // Επιστημονικά Υπεύθυνος
        if(isset($filters['supervisoruserid'])) {
            $this->joinSp($qb);
            $qb->join('sp._subprojectsupervisor', 'sv');
            $qb->andWhere('sv._userid = \''.$filters['supervisoruserid'].'\'');
        }
        // Συντάκτης
        if(isset($filters['authorid'])) {
            $this->joinAuthors($qb);
            $qb->andWhere('emp._recordid = :authorid');
            $qb->setParameter('authorid', $filters['authorid']);
        }
        // Ανάδοχος
        if(isset($filters['contractorid'])) {
            $this->joinContractor($qb);
            $qb->andWhere('contractor._recordid = :contractorid');
            $qb->setParameter('contractorid', $filters['contractorid']);
        }
        // Συντάκτης/Ανάδοχος βάσει ΑΦΜ
        if(isset($filters['afm'])) {
            $this->joinAuthors($qb);
            $this->joinContractor($qb);
            $qb->leftJoin('emp._employee', 'empp');
            $qb->leftJoin('contractor._agency', 'agency');
            $qb->andWhere('empp._afm = :afm OR agency._afm = :afm');
            $qb->setParameter('afm', $filters['afm']);
        }
        // Αναζήτηση
        if(isset($filters['search']) && $filters['search'] != "") {
            $this->addSearchFilter($qb, $filters['search']);
        }

        // Ordering
        $sort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort');
        $order = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        if(isset($sort)) {
            $this->createOrderByQuery($qb, $sort, $order, 'd');
        } else {
            $qb->orderBy('d._title', 'ASC');
        }
        return $this->getResult($qb);
    }

    protected function joinSp(Doctrine\ORM\QueryBuilder &$qb) {
        if($this->_spjoined == false) {
            $qb->join('d._workpackage', 'wp');
            $qb->join('wp._subproject', 'sp');
            $this->_spjoined = true;
        }
    }

    protected function joinAuthors(Doctrine\ORM\QueryBuilder &$qb) {
        if($this->_authorsjoined == false) {
            $qb->leftJoin('d._authors', 'authors');
            $qb->leftJoin('authors._employee', 'emp');
            $this->_authorsjoined = true;
        }
    }

    protected function joinContractor(Doctrine\ORM\QueryBuilder &$qb) {
        if($this->_contractorjoined == false) {
            $qb->leftJoin('d._contractor', 'contractor');
            $this->_contractorjoined = true;
        }
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('d._title LIKE :searchterms');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }

    public function findOverdueDeliverables() {
        $em = Zend_Registry::get('entityManager');
        $qb = $em->createQueryBuilder();
        $qb->select('d');
        $qb->from('Erga_Model_SubItems_Deliverable', 'd');
        $qb->innerJoin('d._workpackage', 'wp');
        $qb->innerJoin('wp._subproject', 'sp');

        // Όπου η endDate έχει περάσει και η approvalDate είναι NULL
        $qb->andWhere('d._completionapprovaldate IS NULL AND d._enddate < CURRENT_DATE()');

        return $this->getResult($qb);
    }
}
?>