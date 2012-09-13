<?php
use Doctrine\ORM\EntityRepository;

abstract class Application_Model_Repositories_BaseRepository extends EntityRepository {
    protected $_ldapConn;
    protected $_getQb = false;

    /**
     * @return Zend_Ldap
     */
    protected function getLdapConn() {
        if(!isset($this->_ldapConn)) {
            // Ανάκτηση του config
            $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $options = $bootstrap->getOptions();
            $options = $options['ldap']['server1'];
            $this->_ldapConn = new Zend_Ldap($options);
            $this->_ldapConn->connect();
            $this->_ldapConn->bind($options['username'], $options['password']);
        }
        return $this->_ldapConn;
    }

    protected function createOrderByQuery(Doctrine\ORM\QueryBuilder &$qb, $sort, $order, $prefix) {
        $options[0] = strstr($sort, '_', true);
        if(strpos($sort, '_') !== false) { // Αν υπάρχουν υπο-options
            $options[1] = substr(strstr($sort, '_'), 1);
            $newprefix = $prefix.'a';
            $qb->join($prefix.'._'.$options[0], $newprefix);
            $this->createOrderByQuery($qb, $options[1], $order, $newprefix);
        } else {
            $qb->orderBy($prefix.'._'.$sort, $order);
        }
    }
    
    public function __call($method, $arguments) {
        if(strtolower(substr($method, -2)) == 'qb') {
            $oldgetquery = $this->_getQb;
            $this->_getQb = true;
            $method = substr($method, 0, strlen($method) - 2);
            if(method_exists($this, $method)) {
                // Το switch χρησιμοποιείται γιατί είναι πιο γρήγορο από την call_user_func_array
                switch(count($arguments)) {
                    case 0: $result = $this->{$method}(); break;
                    case 1: $result = $this->{$method}($arguments[0]); break;
                    case 2: $result = $this->{$method}($arguments[0], $arguments[1]); break;
                    case 3: $result = $this->{$method}($arguments[0], $arguments[1], $arguments[2]); break;
                    case 4: $result = $this->{$method}($arguments[0], $arguments[1], $arguments[2], $arguments[3]); break;
                    case 5: $result = $this->{$method}($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]); break;
                    default: $result = call_user_func_array($method, $arguments);  break;
                }
            } else {
                $result = parent::__call($method, $arguments);
            }
            $this->_getQb = $oldgetquery;
            return $result;
        } else {
            return parent::__call($method, $arguments);
        }
    }

    /**
     * Αυτή η συνάρτηση επιστρέφει είτε τα resultset είτε το querybuilder που
     * του δόθηκε σαν argument (αν το property _getQb είναι true)
     * @param Doctrine\ORM\QueryBuilder $qb
     */
    protected function getResult(Doctrine\ORM\QueryBuilder $qb) {
        if($this->_getQb == true) {
            return $qb;
        } else {
            return $qb->getQuery()->getResult();
        }
    }
}
?>