<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            //case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->_forward('notfound');
                return;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = $errors->exception->getMessage();
                break;
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
            $data = array();
            if(Zend_Auth::getInstance()->hasIdentity()) {
                $data['subject'] = 'Exception Report - '.Zend_Auth::getInstance()->getStorage()->read()->get_userid();
            } else {
                $data['subject'] = 'Exception Report - Ανώνυμος Χρήστης';
            }
            $data['description'] = $errors->exception;
            // Δεν ελέγχουμε αν μπήκε με επιτυχία ή όχι γιατί αν δε μπήκε δε μπορούμε να κάνουμε τίποτε άλλο
            if(APPLICATION_ENV === 'production') {
                $this->_helper->createRedmineIssue($this, $data);
            }
        }
    }
    
    public function notfoundAction() {
        $this->getResponse()->setHttpResponseCode(404);
        $priority = Zend_Log::NOTICE;
        $this->view->pageTitle = '404 - Η σελίδα δεν βρέθηκε';
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

