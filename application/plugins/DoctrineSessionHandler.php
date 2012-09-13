<?php
class Application_Plugin_DoctrineSessionHandler implements Zend_Session_SaveHandler_Interface
{
        private $_sessionName;
        private $_session;
        private $_lifetime;
        /**
         * @var \Doctrine\ORM\EntityManager
         */
        private $_em;

        public function __construct($lifetime = null)
        {
                if(is_null($lifetime))
                {
                        $this->_lifetime = (int) ini_get('session.gc_maxlifetime');
                }
                else
                {
                        $this->_lifetime = (int) $lifetime;
                }
                $this->_em = Zend_Registry::get("entityManager");
        }

        public function setLifetime($lifetime)
        {
                $this->_lifetime = $lifetime;
        }

        public function getLifetime()
        {
                return $this->_lifetime;
        }

        public function read($id)
        {
                $this->_session = $this->_em->find('Application_Plugin_DoctrineSession', $id);

                if(empty($this->_session))
                {
                        $this->_session = new Application_Plugin_DoctrineSession();
                        $this->_session->id = $id;
                        $this->_session->lifetime = $this->_lifetime;
                        $this->_session->modified = time();
                        $this->_em->persist($this->_session);

                        return '';
                }
                return $this->_session->data;
        }

        public function write($id, $data)
        {
                $this->_session->data = $data;
                $this->_session->modified = time();
                $this->flush();

                return true;
        }

        public function destroy($id)
        {
                if($this->_session->id == $id)
                {
                        $this->_em->remove($this->_session);
                        $this->flush();

                        return true;
                }
                return false;
        }

        public function gc($maxlifetime)
        {
                $this->_em->createQuery('DELETE Application_Plugin_DoctrineSession s WHERE s.modified < ('.time().' - s.lifetime)')->execute();
                include_once(APPLICATION_PATH.'/modules/praktika/models/Competition.php');
                $this->_em->createQuery('DELETE Praktika_Model_Competition c WHERE c._subproject IS NULL AND c._aitisi IS NULL')->execute();
                $orphanedconsultants = $this->_em->createQuery('SELECT c FROM Application_Model_Consultant c LEFT JOIN c._astechnicalconsultant atc LEFT JOIN c._asresponsibleperson arp WHERE atc IS NULL AND arp IS NULL')->getResult();
                foreach($orphanedconsultants as &$curOrphan) {
                    $this->_em->remove($curOrphan);
                }
                $this->flush();
                $this->_em->getRepository('Application_Model_User')->garbageCollection(); // Διαγραφή χρηστών παλιότερων από 30 ημέρες (αν είναι ορφανοί)
        }

        public function open($save_path, $name)
        {
                $this->_sessionName = $name;
                return true;
        }

        public function close()
        {
                return true;
        }
        
        protected function flush() {
            if(!$this->_em->isOpen()) {
                $this->_em = $this->_em->create($this->_em->getConnection(), $this->_em->getConfiguration(), $this->_em->getEventManager());
            }
            $this->_em->flush();
        }
}

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 * @Entity @Table(name="sessions")
 */
class Application_Plugin_DoctrineSession {
    /**
     * @Id
     * @Column (name="id", type="string")
     */
    protected $id;
    /**
     * @Column (name="modified", type="integer")
     */
    protected $modified;
    /**
     * @Column (name="lifetime", type="integer")
     */
    protected $lifetime;
    /**
     * @Column (name="data", type="string")
     */
    protected $data;
    
    public function __set($name, $value) {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        return $this->$method();
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getModified() {
        return $this->modified;
    }

    public function setModified($modified) {
        $this->modified = $modified;
    }

    public function getLifetime() {
        return $this->lifetime;
    }

    public function setLifetime($lifetime) {
        $this->lifetime = $lifetime;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }
}
?>