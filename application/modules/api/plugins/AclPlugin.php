<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Plugin_AclPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ('api' != $request->getModuleName()) {
            // Αν δεν είμαστε στο module του API τότε επιστρέφουμε
            return;
        }
        // Skip synedriaseisee and synedriaseisee_subjects controller
        if(strtolower($request->getControllerName()) === 'synedriaseisee' || strtolower($request->getControllerName()) === 'synedriaseisee_subjects') {
            return;
        }
        // Skip schema action
        if(strtolower($request->getParam('id')) === 'schema') {
            return;
        }
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()) {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="Teiath"');
                Zend_Registry::set('user', false);
                $this->redirectToError(new Exception('AuthorizationRequired', 401));
                return;
            } else {
                $credentials = array('username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']);
                $user = Application_Model_User::authenticate($credentials);
                if($user != false) {
                    $auth->getStorage()->write($user);
                } else {
                    $this->redirectToError(new Exception('InvalidCredentials', 401));
                    return;
                }
            }
        }
    }
    
    protected function redirectToError($exception) {
        // Repoint the request to the default error handler
        $request = $this->getRequest();
        $request->setControllerName('error');
        $request->setActionName('error');

        // Set up the error handler
        $error = new Zend_Controller_Plugin_ErrorHandler();
        $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
        $error->request = clone($request);
        $error->exception = $exception;
        $request->setParam('error_handler', $error);
    }
}
?>