<?php
$time = microtime(true);
$memory = memory_get_usage();

// Define path to application directory
if(DIRECTORY_SEPARATOR !== '/') {
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)) . '/..'));
} else {
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
}

// Define application environment
define('APPLICATION_ENV', 'development');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

register_shutdown_function('__shutdown');

function __shutdown() {
global $time, $memory;
$endTime = microtime(true);
$endMemory = memory_get_usage();

echo '
Time [' . ($endTime - $time) . '] Memory [' . number_format(( $endMemory - $memory) / 1024) . 'Kb]';
}
?>