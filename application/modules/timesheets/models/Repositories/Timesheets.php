<?php
use DoctrineExtensions\Paginate\Paginate;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_Model_Repositories_Timesheets extends Application_Model_Repositories_BaseRepository
{
    /**
     * @return Timesheets_Model_Timesheet
     */
    public function findTimesheets($filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('t');
        $qb->from('Timesheets_Model_Timesheet', 't');

        // Βασικά φίλτρα
        // Κωδικός Χρήστη
        if(isset($filters['userid'])) {
            $qb->join('t._employee', 'e');
            $qb->join('e._employee', 'ee');
            $qb->andWhere('ee._ldapusername = :userid');
            $qb->setParameter('userid', $filters['userid']);
        }
        // Κωδικός Χρήστη
        if(isset($filters['supervisoruserid'])) {
            $qb->join('t._project', 'p');
            $qb->join('p._basicdetails', 'bd');
            $qb->join('bd._supervisor', 'spv');
            $qb->andWhere('spv._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }

        return $this->getResult($qb);
    }

    public function getEmployeesAggregate($filters = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('Timesheets_Model_Timesheet', 't');
        $qb->innerJoin('t._activities', 'a');
        $this->addPaidAmountJoins($qb);

        // Φίλτρα
        $this->addFilters($qb, $filters);

        // Grouping
        $amountquery = 'SUM(a._end - a._start) as hours, SUM((a._end - a._start)*author._rate) as paidamount';
        $this->addGroupby($qb, $amountquery, 'employee');

        return $this->getResult($qb);
    }

    public function getHours($filters = array(), $groupBy = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('Timesheets_Model_Timesheet', 't');
        $qb->innerJoin('t._activities', 'a');

        // Φίλτρα
        $this->addFilters($qb, $filters);
        
        // Grouping
        $amountquery = 'SUM(TIMEDIFFSEC(a._end, a._start)/3600) as hours';
        if(isset($groupBy)) {
            $this->addGroupby($qb, $amountquery, $groupBy);
        } else {
            $qb->select($amountquery);
        }

        return $qb->getQuery()->getResult($filters['hydrate']);
    }

    public function getPaidAmount($filters = array(), $groupBy = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('Timesheets_Model_Timesheet', 't');
        $qb->innerJoin('t._activities', 'a');
        $this->addPaidAmountJoins($qb);

        // Φίλτρα
        $this->addFilters($qb, $filters);
        
        // Grouping
        $amountquery = 'SUM((a._end - a._start)*author._rate) as paidamount';
        if(isset($groupBy)) {
            $this->addGroupby($qb, $amountquery, $groupBy);
        } else {
            $qb->select($amountquery);
        }

        return $qb->getQuery()->getResult($filters['hydrate']);
    }
    
    public function getHoursAndPaidAmount($filters = array(), $groupBy = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('Timesheets_Model_Timesheet', 't');
        $qb->innerJoin('t._activities', 'a');
        $this->addPaidAmountJoins($qb);

        // Φίλτρα
        $this->addFilters($qb, $filters);
        
        // Grouping
        $amountquery = 't as timesheet, SUM(a._end - a._start) as hours, SUM((a._end - a._start)*author._rate) as paidamount';
        $qb->join('t._project', 'pp');
        if(isset($groupBy)) {
            $this->addGroupby($qb, $amountquery, $groupBy);
        } else {
            $qb->select($amountquery);
        }

        return $qb->getQuery()->getResult($filters['hydrate']);
    }

    protected function addFilters(Doctrine\ORM\QueryBuilder &$qb, &$filters) {
        // Μας ενδιαφέρουν ΜΟΝΟ τα εγκεκριμένα φύλλα
        $qb->andWhere('t._approved = '.Timesheets_Model_Timesheet::APPROVED);
        // ΑΦΜ Απασχολούμενου
        if(isset($filters['afm'])) {
            $qb->join('t._employee', 'e');
            $qb->join('e._employee', 'ee');
            $qb->andWhere('ee._afm = :afm');
            $qb->setParameter('afm', $filters['afm']);
        }
        // Κωδικός Σύμβασης
        if(isset($filters['contractid'])) {
            $qb->join('t._employee', 'e');
            $qb->andWhere('e._recordid = :contractid');
            $qb->setParameter('contractid', $filters['contractid']);
        }
        // Κωδικός Έργου
        if(isset($filters['projectid'])) {
            $qb->join('t._project', 'fp');
            $qb->andWhere('fp._projectid = :projectid');
            $qb->setParameter('projectid', $filters['projectid']);
        }
        // Κωδικός Επιστημονικά Υπευθύνου
        if(isset($filters['supervisoruserid'])) {
            $qb->join('a._deliverable', 'fdeliverable');
            $qb->join('fdeliverable._workpackage', 'fworkpackage');
            $qb->join('fworkpackage._subproject', 'fsubproject');
            $qb->join('fsubproject._subprojectsupervisor', 'fsubprojectsupervisor');
            $qb->andWhere('fsubprojectsupervisor._userid = :supervisoruserid');
            $qb->setParameter('supervisoruserid', $filters['supervisoruserid']);
        }
        // Κωδικός Υποέργου
        if(isset($filters['subprojectid'])) {
            $qb->join('a._deliverable', 'fd');
            $qb->join('fd._workpackage', 'fwp');
            $qb->join('fwp._subproject', 'fsp');
            $qb->andWhere('fsp._subprojectid = :subprojectid');
            $qb->setParameter('subprojectid', $filters['subprojectid']);
        }
        // Κατηγορία Προσωπικού
        if(isset($filters['personnelcategoryid'])) {
            $qb->join('author._personnelcategory', 'prsc');
            $qb->andWhere('prsc._recordid = :personnelcategoryid');
            $qb->setParameter('personnelcategoryid', $filters['personnelcategoryid']);
        }
        // Μήνας
        if(isset($filters['month'])) {
            $qb->andWhere('t._month = :month');
            $qb->setParameter('month', $filters['month']);
        }
        // Έτος
        if(isset($filters['year'])) {
            $qb->andWhere('t._year = :year');
            $qb->setParameter('year', $filters['year']);
        }
        if(!isset($filters['hydrate'])) {
            $filters['hydrate'] = Doctrine\ORM\Query::HYDRATE_ARRAY;
        }
    }
    
    protected function addGroupby(Doctrine\ORM\QueryBuilder &$qb, $amountquery, $groupBy = 'both') {
        if($groupBy === 'month') {
            $qb->groupBy('t._month');
            $qb->select('t._month as month, '.$amountquery);
        } else if($groupBy === 'employee') {
            $qb->from('Erga_Model_SubItems_SubProjectEmployee', 'e');
            $qb->andWhere('t._employee = e');
            $qb->from('Application_Model_Employee', 'ee');
            $qb->andWhere('e._employee = ee');
            $qb->groupBy('ee');
            $qb->select('ee, '.$amountquery);
        } else if($groupBy === 'workpackage') {
            $qb->from('Erga_Model_SubItems_WorkPackage', 'wp');
            $qb->andWhere('d._workpackage = wp');
            $qb->groupBy('wp');
            $qb->select('wp._recordid as workpackage, '.$amountquery);
        } else {
            $qb->from('Erga_Model_Project', 'p');
            $qb->andWhere('t._project = p');
            if($groupBy === 'project') {
                $qb->groupBy('p');
                $qb->select('p._projectid as projectid, '.$amountquery);
            } else {
                $qb->groupBy('t._month, p._projectid');
                $qb->select('t._month as month, p._projectid as projectid, '.$amountquery);
            }
        }
    }
    
    protected function addPaidAmountJoins(Doctrine\ORM\QueryBuilder &$qb) {
        $qb->innerJoin('t._employee', 'remployee');
        $qb->innerJoin('a._deliverable', 'd');
        $qb->innerJoin('d._authors', 'author');
        // Βρίσκουμε μόνο τον συντάκτη που αντιστοιχεί στον εργαζόμενο μας //
        $qb->innerJoin('author._employee', 'employee');
        $qb->andWhere('remployee._recordid = employee._recordid');
        /////////////////////////////////////////////////////////////////////
    }
}
?>