<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity (repositoryClass="Application_Model_Repositories_Users") @Table(name="users")
 * @HasLifecycleCallbacks
 */
class Application_Model_User extends Dnna_Model_Object {
    /**
     * @Id
     * @Column (name="userid", type="string")
     * @FormFieldType Hidden
     * @FormFieldDisabled true
     */
    protected $_userid;
    /**
     * @Column (name="realname", type="string")
     * @FormFieldLabel Ονοματεπώνυμο
     * @FormFieldDisabled true
     */
    protected $_realname;
    /**
     * @FormFieldLabel Ιδιότητα
     * @FormFieldDisabled true
     */
    protected $_capacity;
    /**
     * @FormFieldLabel E-mail
     * @FormFieldDisabled true
     */
    protected $_email;
    /**
     * @var Application_Model_Department
     */
    protected $_department;
    /**
     * @FormFieldLabel Τμήμα
     * @FormFieldDisabled true
     */
    protected $_departmentname;
    /**
     * @Column (name="rank", type="string")
     * @FormFieldLabel Βαθμίδα
     */
    protected $_rank;
    /**
     * @Column (name="sector", type="string")
     * @FormFieldLabel Τομέας
     */
    protected $_sector;
    /**
     * @Column (name="phone", type="string")
     * @FormFieldLabel Τηλ./Fax
     */
    protected $_phone;

    protected $_roles;
    /**
     * @Column (name="token", type="string")
     */
    protected $_token;

    /**
     * @var Application_Model_Employee 
     */
    protected $_contracts; // Οι συμβάσεις με τις οποίες συνδέεται αυτός ο χρήστης

    /**
     * Ελέγχει αν ο χρήστης υπάρχει στον LDAP
     * @return boolean
     */
    public function existsInLDAP() {
        if(isset($this->_userid)) {
            $user = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->find($this->get_userid());
            if($user != null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @postLoad
     * @prePersist
     */
    public function loadFromLDAP() {
        if(!isset($this->_realname) && isset($this->_userid)) {
            $this->setOptions(Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->find($this->_userid)->toArray());
        }
    }
    
    private static function getAuthAdapter(array $params, $options) {
            $options = $options['ldap'];
            $authAdapter = new Zend_Auth_Adapter_Ldap($options);
            $authAdapter->setIdentity($params['username']);
            $authAdapter->setCredential($params['password']);

            return $authAdapter;
        }

    public static function authenticate($credentials = array()) {

        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();
        foreach($options['login']['ignoreSuffix'] as $curSuffix) {
            $credentials['username'] = str_replace($curSuffix, '', $credentials['username']);
        }
        $adapter = self::getAuthAdapter($credentials, $options);
        $result = $adapter->authenticate($adapter);

        // TODO DEBUG
        /*$messages = $result->getMessages();

        $logger = new Zend_Log();
        $logger->addWriter(new Zend_Log_Writer_Stream(Zend_Registry::get('cachePath').'/ldap.log'));
        $filter = new Zend_Log_Filter_Priority(Zend_Log::DEBUG);
        $logger->addFilter($filter);

        foreach ($messages as $i => $message) {
            if ($i-- > 1) { // $messages[2] and up are log messages
                $message = str_replace("\n", "\n  ", $message);
                $logger->log("Ldap: $i: $message", Zend_Log::DEBUG);
            }
        }*/
        // TODO END DEBUG

        if (!isset($result) || !$result->isValid()) {
            return false;
        }
        
        $userObject = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->find($credentials['username']);
        if(count($userObject->get_roles()) <= 0) {
            throw new Exception('Ο χρήστης δεν μπόρεσε να συνδεθεί γιατί δεν έχει ρόλους.');
        }

        // Clean up old stuff
        /* @var $cache Zend_Cache_Core */
        $cache = Zend_Registry::get('cache');
        $cache->remove('ldapusersearch_'.$userObject->get_userid());
        if($userObject->get_token() == null || $userObject->get_token() == '') {
            $userObject->regenerateToken();
        }
        $userObject->save();
        
        return $userObject;
    }

    public function hasRole($rolename) {
        foreach($this->get_roles() as $curRole) {
            if($curRole->get_rolename() === $rolename) {
                return true;
            }
        }
        return false;
    }

    public function getDominantRole() {
        if($this->hasRole('elke')) {
            return 'elke';
        } else if($this->hasRole('professor')) {
            return 'professor';
        } else if($this->hasRole('employee')) {
            return 'employee';
        } else {
            throw new Exception('Σφάλμα στην ανάκτηση του κυρίαρχου ρόλου του χρήστη');
        }
    }

    public function get_userid() {
        return $this->_userid;
    }

    public function set_userid($_userid) {
        $this->_userid = $_userid;
    }

    public function get_realname() {
        return $this->_realname;
    }
    
    public function get_realnameLowercase() {
        $str = mb_convert_case($this->get_realname(), MB_CASE_TITLE, "UTF-8").' ';
        $str = preg_replace('/σ\s/i', 'ς ', $str);
        return trim($str);
    }

    public function get_realnameCondensed() {
        return str_replace(" ", "", $this->get_realname());
    }

    public function set_realname($_realname) {
        $this->_realname = $_realname;
    }

    public function get_capacity() {
        return $this->_capacity;
    }

    public function set_capacity($_capacity) {
        $this->_capacity = $_capacity;
    }

    public function get_rank() {
        return $this->_rank;
    }

    public function set_rank($_rank) {
        $this->_rank = $_rank;
    }

    public function get_department() {
        return $this->_department;
    }

    public function set_department($_department) {
        $this->_department = $_department;
    }

    public function get_departmentname() {
        if(isset($this->_department)) {
            $this->_departmentname = $this->_department->__toString();
        }
        return $this->_departmentname;
    }

    public function get_sector() {
        return $this->_sector;
    }

    public function set_sector($_sector) {
        $this->_sector = $_sector;
    }

    public function get_phone() {
        return $this->_phone;
    }

    public function set_phone($_phone) {
        $this->_phone = $_phone;
    }

    public function get_email() {
        /*if(!isset($this->_email)) {
            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
            $this->_email = $config['admin']['email']['toaddress'];
        }*/
        return $this->_email;
    }

    public function set_email($_email) {
        $this->_email = $_email;
    }

    public function get_roles() {
        return $this->_roles;
    }

    public function set_roles($_roles) {
        $this->_roles = $_roles;
    }

    public function get_token() {
        return $this->_token;
    }

    public function get_contracts() {
        $this->_contracts = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->findEmployeeByLdapUsername($this->get_userid());
        return $this->_contracts;
    }

    public function regenerateToken() {
        $this->_token = md5(time().serialize($this));
    }

    public function __toString() {
        return $this->get_realname();
    }
}
?>
