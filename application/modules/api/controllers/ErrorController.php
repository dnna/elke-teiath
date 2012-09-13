<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_ErrorController extends Api_IndexController
{
    const noindex = true;

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $this->view->errorCode = $errors->exception->getCode();

        switch ($errors->type) {
            //case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->errorCode = 404;
                $this->view->message = 'Page not found';
                break;
            default:
                if($this->view->errorCode == 401) {
                    $this->getResponse()->setHttpResponseCode(401);
                } else if($this->view->errorCode == 0) {
                    // application error
                    $this->getResponse()->setHttpResponseCode(500);
                    $priority = Zend_Log::CRIT;
                    $this->view->errorCode = 500;
                }
                $this->view->message = 'Application error';
                $redmineIssue = true;
                break;
        }

        if($this->view->errorCode != 0) {
            //$this->getResponse()->setHttpResponseCode($this->view->errorCode);
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;

        // Δημιουργία αυτοματοποιημένου report στο Redmine (αν έχει ενεργοποιηθεί)
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        if(isset($config['report']) && isset($config['report']['redmineUrl'])) {
            if(isset($redmineIssue) &&$redmineIssue == true) {
                $data = array();
                if(Zend_Auth::getInstance()->hasIdentity()) {
                    $data['subject'] = 'API Exception Report - '.Zend_Auth::getInstance()->getStorage()->read()->get_userid();
                } else {
                    $data['subject'] = 'API Exception Report - Ανώνυμος Χρήστης';
                }
                $data['description'] = $errors->exception;
                // Δεν ελέγχουμε αν μπήκε με επιτυχία ή όχι γιατί αν δε μπήκε δε μπορούμε να κάνουμε τίποτε άλλο
                if(APPLICATION_ENV === 'production') {
                    $this->_helper->createRedmineIssue($this, $data);
                }
            }
        }
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}