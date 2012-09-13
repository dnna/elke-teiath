<?php
use Doctrine\ORM\EntityRepository;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Model_Repositories_Users extends Application_Model_Repositories_BaseRepository {
    protected $_config;

    public function __construct($em, Doctrine\ORM\Mapping\ClassMetadata $class) {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_config = $bootstrap->getOptions();
        parent::__construct($em, $class);
    }

    /**
     * Sanitizes ldap search strings.
     * See rfc2254
     * @link http://www.faqs.org/rfcs/rfc2254.html
     * @since 1.5.1 and 1.4.5
     * @param string $string
     * @return string sanitized string
     * @author Squirrelmail Team
     */
    protected function ldapspecialchars($string) {
        $sanitized=array('\\' => '\5c',
                         '*' => '\2a',
                         '(' => '\28',
                         ')' => '\29',
                         "\x00" => '\00');

        return str_replace(array_keys($sanitized),array_values($sanitized),$string);
    }

    /**
     * Επιστρέφει τον υπάρχοντα συνδεδεμένο χρήστη, ή null αν δεν
     * έχει αυθεντικοποιηθεί.
     * @return Application_Model_User
     */
    public function getCurrentUser() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()) {
            return null;
        } else {
            return $auth->getStorage()->read();
        }
    }

    /**
     * @return Array(Application_Model_User) 
     */
    public function findByName($name, $limit = 10) {
        // Αναζήτηση του χρήστη στον LDAP
        $ldapUserSearch = new Zend_Session_Namespace('LDAPUserSearch');
        $auth = Zend_Auth::getInstance();
        /* @var $cache Zend_Cache_Core */
        $cache = Zend_Registry::get('cache');
        if($auth->hasIdentity()) {
            $cacheid = 'ldapusersearch_'.$auth->getStorage()->read()->get_userid();
        } else {
            $cacheid = 'ldapusersearch_anonymous';
        }
        if($cache->load($cacheid) == false) { // Δεν βρέθηκε στο cache
            $cache->save(array('val' => true), $cacheid);
            $userArray = array();
                try {
                $ldap=$this->getLdapConn();
                $options = $ldap->getOptions();
                $attributes = array('uid', 'cn;lang-el', 'mail', 'eduPersonAffiliation', $this->_config['ldapopts']['departmentAttr']);
                if($name != "") {
                    $name = $this->ldapspecialchars($name);
                }
                $filter = str_replace('%name%', $name, $this->_config['ldapopts']['facultySearchFilter']);
                $filter = str_replace('**', '*', $filter);
                $search = @ldap_search($ldap->getResource(), 'ou=people,dc=teiath,dc=gr', $filter, $attributes, 0, $limit, 1);
                $users = ldap_get_entries($ldap->getResource(), $search);
                foreach ($users as $key => $user) {
                    if($key !== 'count') {
                        $newuser = $this->createUserFromLDAPEntry($user);
                        if($newuser != null) { // Null σημαίνει ότι ο χρήστης είχε λάθος μορφή στον LDAP
                            $userArray[] = $newuser;
                        }
                    }
                }
                $ldapUserSearch->previousResults = $userArray;
            } catch(Exception $e) {
                echo 'Exception στη συνάρτηση Application_Model_Repositories_Users::findByName()';
                //echo $e.'<BR>';
            }
            $cache->remove($cacheid);
            return $userArray;
        } else {
            if(isset($ldapUserSearch->previousResults)) {
                return $ldapUserSearch->previousResults;
            } else {
                return array();
            }
        }
    }

    /**
     * @return Application_Model_User
     */
    public function find($id) {
        if(is_array($id) && isset($id['_userid'])) {
            $id = $id['_userid'];
        }
        // Search LDAP
        $ldap=$this->getLdapConn();
        $attributes = array('uid', 'cn;lang-el', 'mail', 'eduPersonAffiliation', $this->_config['ldapopts']['departmentAttr']);
        $filter = str_replace('%name%', $this->ldapspecialchars($id), $this->_config['ldapopts']['userSearchFilter']);
        $filter = str_replace('**', '*', $filter);
        $search = @ldap_search($ldap->getResource(), 'ou=people,dc=teiath,dc=gr', $filter, $attributes, 0);
        $userArray = ldap_get_entries($ldap->getResource(), $search);
        if(isset($userArray[0])) {
            return $this->createUserFromLDAPEntry($userArray[0]);
        } else {
            return null;
        }
    }

    /**
     * @return Application_Model_User
     */
    public function findByToken($token) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u');
        $qb->from('Application_Model_User', 'u');

        $qb->andWhere('u._token = :token');
        $qb->setParameter('token', $token);
        try {
            $user = $qb->getQuery()->getSingleResult();
            return $this->find($user->get_userid());
        } catch(Exception $e) {
            return null;
        }
    }

    protected function getUserRoles($ldapArray) {
        $roles = array();
        $deptid = $ldapArray[strtolower($this->_config['ldapopts']['departmentAttr'])][0];
        if((is_array($this->_config['ldapopts']['elkeDepartmentId']) && in_array($deptid, $this->_config['ldapopts']['elkeDepartmentId'])) || $this->_config['ldapopts']['elkeDepartmentId'] === $deptid) {
            $roles[] = $this->_em->getRepository('Application_Model_UserRole')->find(1); // ΕΛΚΕ
        } else if($ldapArray['edupersonaffiliation'][0] === 'faculty') {
            $roles[] = $this->_em->getRepository('Application_Model_UserRole')->find(0); // Καθηγητής
        }
        $roles[] = $this->_em->getRepository('Application_Model_UserRole')->find(2); // Απασχολούμενος
        return $roles;
    }

    protected function getUserCapacity($ldapArray) {
        $edupersonaffiliation = $ldapArray['edupersonaffiliation'][0];
        if($edupersonaffiliation === 'faculty') {
            return 'Εκπαιδευτικός';
        } else if($edupersonaffiliation === 'staff' || $edupersonaffiliation === 'employee') {
            return 'Διοικητικός';
        } else if($edupersonaffiliation === 'student') {
            return 'Σπουδαστής';
        } else if($edupersonaffiliation === 'alum') {
            return 'Απόφοιτος';
        } else if($edupersonaffiliation === 'affiliate') {
            return 'Συνεργαζόμενος';
        } else if($edupersonaffiliation === 'member' || $edupersonaffiliation === 'library-walk-in') {
            return 'Who knows?';
        }
    }
    
    protected function getDepartment($ldapArray) {
        $deptid = $ldapArray[strtolower($this->_config['ldapopts']['departmentAttr'])][0];
        return $this->_em->getRepository('Application_Model_Department')->find($deptid);
    }

    protected function createUserFromLDAPEntry($ldapArray) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u');
        $qb->from('Application_Model_User', 'u');
        $qb->andWhere("u._userid = :userid");
        $qb->setParameter('userid', $ldapArray['uid'][0]);
        if(!isset($ldapArray['cn;lang-el'][0])) {
            return null;
        }
        $mapping = Array(
                'realname' => $ldapArray['cn;lang-el'][0],
                'email' => $ldapArray['mail'][0],
                'capacity' => $this->getUserCapacity($ldapArray),
                );
        try {
            $userObject = $qb->getQuery()->getSingleResult();
            $userObject->setOptions($mapping);
            $userObject->set_roles($this->getUserRoles($ldapArray));
            $userObject->set_department($this->getDepartment($ldapArray));
        } catch(Exception $e) { // TODO Τι exception ακριβώς;
            $userObject = new Application_Model_User($mapping);
            $userObject->set_userid($ldapArray['uid'][0]);
            $userObject->set_roles($this->getUserRoles($ldapArray));
            $userObject->set_department($this->getDepartment($ldapArray));
            $this->_em->persist($userObject);
        }

        return $userObject;
    }

    /**
     * Επιστρέφει πίνακα με συνολικά στοιχεία για κάθε επιστημονικά υπεύθυνο. Το
     * αποτέλεσμα έχει την εξής μορφή:
     * Το index 0 έχει τον επιστημονικά υπεύθυνο (σαν αντικείμενο)
     * Το index 'projectscount' έχει τον αριθμό των έργων στα οποία έχει
     * καταχωρηθεί ο επιστημονικά υπεύθυνος
     * Το index 'totalamount' έχει την συνολική αμοιβή του επιστημονικά
     * υπευθύνου από όλα τα υποέργα (ειναι Αμερικάνικο float, οπότε ίσως θέλει
     * conversion)
     * @return array Τα προαναφερόμενα
     */
    public function getSupervisorsAggregate($filters = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u, count(DISTINCT p) as projectscount');
        $qb->from('Erga_Model_ProjectBasicDetails', 'bd');
        $qb->from('Application_Model_User', 'u');
        $qb->from('Erga_Model_Project', 'p');
        $qb->andWhere('bd._supervisor = u');
        $qb->andWhere('bd._project = p');
        $qb->groupBy('u._userid');
        if(isset($filters['user']) && $filters['user'] instanceof Application_Model_User) {
            $qb->andWhere('u._userid = \''.$filters['user']->get_userid().'\'');
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
            } else {
                $this->createOrderByQuery($qb, $sort, $order, 'u');
            }
        } else {
            $qb->orderBy('u._realname', 'ASC');
        }

        return $this->getResult($qb);
    }

    protected function addSearchFilter(Doctrine\ORM\QueryBuilder &$qb, $searchterms = "") {
        $qb->andWhere('(u._realname LIKE :searchterms)');
        $qb->setParameter('searchterms', '%'.$searchterms.'%');
    }

    public function getSupervisorOverview(Application_Model_User $user, $filters = array()) {
        $overview = array();
        $supervisor = $this->getSupervisorsAggregate(array('user' => $user) + $filters);
        $overview['supervisor'] = $supervisor[0];
        $overview['projects'] = $this->getSupervisorProjects($user, $filters);
        $overview['subprojects'] = $this->getSupervisorSubProjects($user, $filters);
        return $overview;
    }

    public function getSupervisorSubProjects(Application_Model_User $user, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sp');
        $qb->from('Erga_Model_SubProject', 'sp');
        $qb->innerJoin('sp._subprojectsupervisor', 's');
        $qb->andWhere('s._userid = :id');
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργαs
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->join('sp._parentproject', 'pp');
            $qb->andWhere('pp._iscomplete = FALSE');
        }

        $qb->setParameter('id', $user->get_userid());

        return $this->getResult($qb);
    }

    public function getSupervisorProjects(Application_Model_User $user, $filters = array()) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p');
        $qb->from('Erga_Model_Project', 'p');
        $qb->innerJoin('p._basicdetails', 'bd');
        $qb->innerJoin('bd._supervisor', 's');
        $qb->andWhere('s._userid = :id');
        // Υπολογισμός μόνο για τα τρέχοντα (μη ολοκληρωμένα) έργα
        if(isset($filters['currentprojects']) && $filters['currentprojects'] == 'true') {
            $qb->andWhere('p._iscomplete = FALSE');
        }

        $qb->setParameter('id', $user->get_userid());

        return $this->getResult($qb);
    }

    public function garbageCollection() {
        // Σβήνει τις εγγραφές χρηστών που δεν υπάρχουν στον LDAP
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u');
        $qb->from('Application_Model_User', 'u');
        $users = $this->getResult($qb);
        $toRemove = array();
        foreach($users as $user) {
            if(!$user->existsInLDAP()) {
                $toRemove[] = "'".$user->get_userid()."'";
            }
        }
        // TODO ίσως σε νεότερες εκδόσεις του Doctrine να μπορεί να γίνει χωρίς το native SQL query
        if($this->_em->getConnection()->getDriver()->getName() !== "pdo_mysql") {
            throw new Exception('Garbage collection χρηστών μπορεί να γίνει μόνο σε MySQL.');
        }
        if(count($toRemove) > 0) {
            $this->_em->flush(); // Για να μην υπάρξει κανένα πρόβλημα με αλλαγές που δεν περάστηκαν
            $sql = "DELETE IGNORE u FROM users u WHERE u.userid IN (".implode(', ', $toRemove).");";
            $this->_em->getConnection()->executeUpdate($sql);
        }
    }
}
?>