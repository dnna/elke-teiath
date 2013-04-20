<?php

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);
ini_set("display_errors", 1);

// Fix HTTP Auth
if(isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] != "") {
    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
} else if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] != "") {
    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
} else if(isset($_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION'] != "") {
    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION'], 6)));
}

// Shutdown function to prevent APC bug #16745 (fatal error in sessions)
function shutdown() {
    session_write_close();
}
register_shutdown_function('shutdown');

// Define path to application directory
if(DIRECTORY_SEPARATOR !== '/') {
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)) . '/../application'));
} else {
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
}

// Define application environment
if(strpos($_SERVER['SERVER_NAME'], 'teiath.gr') !== false) {
    define('APPLICATION_ENV', 'production');
} else {
    define('APPLICATION_ENV', 'development');
}
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
try{
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
    );
    $application->bootstrap()
                ->run();
} catch(Exception $e) {
    echo '<html>
            <head>
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
            <meta http-equiv="Content-Language" content="el">
            </head>
            <body>
                <center>
                    Παρουσιάστηκε σημαντικό σφάλμα κατά την εκκίνηση της εφαρμογής.<BR>Ο κωδικός σφάλματος ήταν:<BR>'.$e->getCode().' '.$e->getMessage().
                '</center>
            </body>
           </html>';
}