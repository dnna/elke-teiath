<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initAutoloader() {
        $options = $this->getOptions();
        $loader = new Zend_Application_Module_Autoloader(array(
                    'basePath' => APPLICATION_PATH,
                    'namespace' => $options['appnamespace'],
                ));
    }

    protected function _initCache() {
        $frontendOptions = array(
            //'lifetime' => 7200, // cache lifetime of 2 hours
            'lifetime' => 0,
            'automatic_serialization' => true
        );

        // Μετατρέπουμε το directory separator σε Unix based (παίζει και στα Windows έτσι)
        if (DIRECTORY_SEPARATOR !== '/') {
            $cachedir = str_replace(DIRECTORY_SEPARATOR, '/', realpath(sys_get_temp_dir()));
        } else {
            $cachedir = realpath(sys_get_temp_dir());
        }
        Zend_Registry::set('cachePath', $cachedir);
        $backendOptions = array(
            'cache_dir' => $cachedir
        );

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        //if(APPLICATION_ENV === 'development') {
            $cache->clean('all'); // Δεν θέλουμε cache στο development
        /*} else {
            // Plugin Cache
            $classFileIncCache = APPLICATION_PATH . '/../data/pluginLoaderCache.php';
            if (file_exists($classFileIncCache)) {
                include_once $classFileIncCache;
            }
            Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
        }*/
        Zend_Registry::set('cache', $cache);
    }

    protected function _initTimezone() {
        $options = $this->getOptions();
        date_default_timezone_set($options['phpSettings']['date']['timezone']);
    }

    protected function _initZendLocale() {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('el_GR'));
    }

    protected function _initRssRoute() {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $route = new Application_Plugin_Route(array(), $front->getDispatcher(), $front->getRequest());
        $router->addRoute('feed', $route);
    }

    protected function _initDoctrine() {
        if ($this->hasPluginResource('doctrine2')) {
            //doctrine autoloader
            include_once(APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php'); // Για να μη βγάζει error στο Γερμανικό server
            $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', APPLICATION_PATH . '/../library/');
            $classLoader->register();
            $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', APPLICATION_PATH . '/../library/');
            $classLoader->register();
            $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', APPLICATION_PATH . '/../library/');
            $classLoader->register();
            include_once('Doctrine/DBAL/Event/Listeners/MysqlSessionInit.php'); // Για να μη βγάζει error σε κάποια servers

            include_once(APPLICATION_PATH . '/plugins/GreekFloatType.php'); // Για να φορτώσει το GreekFloatType
            include_once(APPLICATION_PATH . '/plugins/GreekPercentageType.php'); // Για να φορτώσει το GreekPercentageType
            include_once(APPLICATION_PATH . '/plugins/EDateTimeType.php'); // Για να φορτώσει το EDateTimeType
            $doctrine2Resource = $this->getPluginResource('doctrine2');
            $doctrine2Resource->init();
            $em = $doctrine2Resource->getEntityManager();
            Zend_Registry::set("entityManager", $em);
            include_once(APPLICATION_PATH . '/plugins/BlobType.php'); // Override BlobType

            // Init extensions
            $classLoader = new \Doctrine\Common\ClassLoader('DoctrineExtensions', APPLICATION_PATH . '/../library/');
            $classLoader->register();
        }
    }

    /**
     * http://code.google.com/p/dnna-zend-lib/
     */
    protected function _initDnnaLib() {
        $loader = new Zend_Application_Module_Autoloader(array(
                    'basePath' => APPLICATION_PATH.'/../library/Dnna',
                    'namespace' => 'Dnna',
                ));
        $loader->addResourceType('Controller', 'controllers/', 'Controller');
        if(class_exists('Doctrine\ORM\EntityManager')) {
            include_once(APPLICATION_PATH . '/../library/Dnna/plugins/PointType.php'); // Load the Point type
            //Assuming the entity manager is in Zend_Registry as entityManager
            $config = Zend_Registry::get('entityManager')->getConfiguration();
            $config->addCustomNumericFunction('DISTANCE', 'Dnna\Doctrine\Types\Distance');
            $config->addCustomNumericFunction('POINT_STR', 'Dnna\Doctrine\Types\PointStr');
            $config->addCustomNumericFunction('TIMEDIFFSEC', 'Dnna\Doctrine\Types\TimeDiffSec');
        }
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH.'/../library/Dnna/controllers/helpers',
                                                      'Dnna_Action_Helper');
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH.'/../library/Dnna/controllers/helpers/Rest',
                                                      'Dnna_Action_Helper_Rest');
        $this->bootstrap('view');
        $this->getResource('view')->addHelperPath(APPLICATION_PATH.'/../library/Dnna/views/helpers', 'Dnna_View_Helper');
    }

    protected function _initSession() {
        Zend_Controller_Front::getInstance()->setParam('bootstrap', $this); // Για να δουλεύει το garbageCollection στον sessionhandler
        Zend_Session::setSaveHandler(new Application_Plugin_DoctrineSessionHandler());
        Zend_Session::start();
        //Zend_Auth::getInstance()->setStorage(new Dnna_Plugin_DoctrineStorage('Application_Model_User'));
    }

    protected function _initViewAndNavigation() {
        $navigationConfig = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');
        $navigation = new Zend_Navigation($navigationConfig);
        Application_Plugin_FixNavigationResources::fixResourceNames($navigation);
        Zend_Registry::set('navigation', $navigation);
        $this->getResource('view')->navigation(Zend_Registry::get('navigation'));
    }

    protected function _initPaginator() {
        $config = $this->getOptions();
        $resultsPerPage = (int) $config['resources']['view']['resultsPerPage'];
        Zend_Paginator::setDefaultItemCountPerPage($resultsPerPage);
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(
                'pagination_control.phtml'
        );
        $this->getResource('view')->addScriptPath(APPLICATION_PATH . '/layouts/scripts');
    }

    protected function _initAcl() {
        // Setup ACL
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('user'), 'guest');
        $acl->addRole(new Zend_Acl_Role('employee'), 'user');
        $acl->addRole(new Zend_Acl_Role('professor'), 'user');
        $acl->addRole(new Zend_Acl_Role('elke'), 'user');
        $acl->add(new Zend_Acl_Resource('guestsection'));
        $acl->add(new Zend_Acl_Resource('usersection'));
        $acl->add(new Zend_Acl_Resource('employeesection'));
        $acl->add(new Zend_Acl_Resource('priviledgedsection'));
        $acl->add(new Zend_Acl_Resource('professorsection'));
        $acl->add(new Zend_Acl_Resource('elkesection'));
        $acl->allow('guest', 'guestsection');
        $acl->allow('user', 'usersection');
        $acl->allow('employee', 'employeesection');
        $acl->allow('professor', 'employeesection');
        $acl->allow('professor', 'priviledgedsection');
        $acl->allow('professor', 'professorsection');
        $acl->allow('elke', 'employeesection');
        $acl->allow('elke', 'priviledgedsection');
        $acl->allow('elke', 'elkesection');
        Zend_Registry::set('acl', $acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);

        // User role
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($auth->getStorage()->read()->getDominantRole());
        } else {
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole('guest');
        }
    }

}

?>