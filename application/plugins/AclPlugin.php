<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract
{
    protected $_rolename;
    protected $_user;

    public function __construct() {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $this->_user = $auth->getStorage()->read();
            $this->_rolename = $auth->getStorage()->read()->getDominantRole();
        } else {
            $this->_rolename = 'guest';
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
         /**
         * Load Navigation view helper
         */
        $oViewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $oNavigation   = $oViewRenderer->view->navigation();
        $oViewRenderer->view->user = $this->_user;

        // Ειδικός κώδικας για τον χειρισμό των tokens
        if($request->getActionName() === 'feed' && $request->getParam('token') != null) {
            // Ελέγχουμε αν ο χρήστης έχει πρόσβαση στο feed με βάση το token
            $user = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->findByToken($request->getParam('token'));
            if(isset($user) && $user instanceof Application_Model_User) {
                $this->_rolename = $user->getDominantRole();
                $oViewRenderer->view->tokenuser = $user;
            } else {
                echo 'Εσφαλμένο token';
                die();
            }
        }

        $oldrenderinvisible = $oNavigation->getRenderInvisible();
        $oldrole = $oNavigation->getRole();
        $oNavigation->setRenderInvisible(true); // Για να μη γίνεται fail στις invisible σελίδες
        $oNavigation->setRole($this->_rolename);
        $container = $oNavigation->getContainer();
        $active = $oNavigation->findActive($oNavigation->getContainer()); // Αυτό κάνει match και module και controller και action
        /*@var $activePage Zend_Navigation_Page_Mvc */   
        $activePage =  @$active['page'];
        // Βρίσκω τα partial matches αν δεν βρέθηκε σελίδα που να τα κάνει match όλα
        if(!isset($activePage)) {
            $activePage = $this->findPartialActivePage($container);
        }
        $oNavigation->setRenderInvisible($oldrenderinvisible);
        $oNavigation->setRole($oldrole);

        $skipModules = array('api');
        $skipControllers = array('Login', 'Polling');
        $accessDenied = true;
        if(isset($activePage) || in_array($request->getModuleName(), $skipModules) || in_array($request->getControllerName(), $skipControllers)) {
            // Ο χρήστης έχει πρόσβαση
            $accessDenied = false;
        }
        if($accessDenied == true) {
            // Ο χρήστης δεν έχει πρόσβαση
            try {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $redirector->direct('index', 'Login', 'default');
            } catch(Exception $e) {
                // Σε περίπτωση που έχουν σταλεί τα headers πιάνουμε το exception και κάνουμε redirect
                echo '<meta http-equiv="refresh" content="1;url='.$oViewRenderer->view->url(array('module' => 'default', 'controller' => 'Login', 'action' => 'index'), null, true).'">Please wait...';
                die();
            }
        }
    }

    protected function findPartialActivePage(Zend_Navigation_Container $container) {
        /* @var $acl Zend_Acl */
        $acl = Zend_Registry::get('acl');
        $pages = $container->findAllBy('module', $this->getRequest()->getModuleName());
        // Βρίσκω τα pages που κάνουν match το module και το controller
        $matchesController = array();
        foreach($pages as $curPage) {
            if($curPage->getController() != null && $curPage->getController() === $this->getRequest()->getControllerName()) {
                $matchesController[] = $curPage;
            }
        }
        // Αν υπάρχουν σελίδες που κάνουν match με το controller τότε επιστρέφουμε την πρώτη
        // αλλιώς επιστρέφουμε την πρώτη από αυτές που έκαναν match το module
        if(count($matchesController) > 0) {
            if($acl->isAllowed($this->_rolename, $matchesController[0]->getResource())) {
                return $matchesController[0];
            } else {
                return null;
            }
        } else if(count($pages) > 0) {
            if($acl->isAllowed($this->_rolename, $pages[0]->getResource())) {
                return $pages[0];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
?>