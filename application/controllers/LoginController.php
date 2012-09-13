<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class LoginController extends Zend_Controller_Action {

    protected $referer;
    protected $_redirector;

    public function init() {
        $this->_helper->layout->disableLayout();

        $this->view->pageTitle = "Αυθεντικοποίηση";
        $this->_redirector = $this->_helper->getHelper('Redirector');
        // Keep the URL of the referer, unless we are in the login page
        $refererSession = new Zend_Session_Namespace('referer');
        if(isset($refererSession->referer)) {
            $this->referer = $refererSession->referer;
        } else {
            $this->referer = 'index';
        }
    }

    public function getForm() {
        return new Application_Form_LoginForm(array(
            'action' => '/login/process',
            'method' => 'post',
        ));
    }

    public function preDispatch() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }

    public function loginAction() {
        $request = $this->getRequest();

        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }

        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }

        // Get our authentication adapter and check credentials
        $formValues = $form->getValues();
        $userObject = Application_Model_User::authenticate($formValues);
        if($userObject == false) {
            // Invalid credentials
            $form->setDescription('Εισάγατε λανθασμένο κωδικό χρήστη ή συνθηματικό.');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
        }
        // We're authenticated! Redirect to whatever page the user came from
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write($userObject);
        if(count($userObject->get_roles()) <= 0) { $this->logoutAction(); $this->loginAction(); return; }
        if($this->referer['action'] !== "i" && $this->referer['controller'] !== "i" && $this->referer['module'] !== "i") {
            if($userObject->hasRole('professor') && ($userObject->get_rank() == null || $userObject->get_rank() == "")) {
                $this->_redirector->gotoSimple('index', 'Profile', 'default', Array('showMsg' => 'yes'));
            } else if($userObject->hasRole('elke') && $this->referer['module'] === 'default' &&
                       $this->referer['controller'] === 'index' && $this->referer['action'] === 'index') {
                // Αν ο χρήστης έχει ρόλο ΕΛΚΕ και κάνει login από την αρχική σελίδα τον στέλνουμε στις Εκκρεμότητες
                $this->_redirector->gotoSimple('index', 'Ekkremotites');
            } else {
                $this->_redirector->gotoSimple($this->referer['action'],
                                               $this->referer['controller'],
                                               $this->referer['module'],
                                               $this->referer); // Επιστρέφουμε από εκεί που ήρθαμε
            }
        } else {
            $this->_redirector->gotoSimple('index');
        }
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        if($this->referer['action'] !== "i" && $this->referer['controller'] !== "i" && $this->referer['module'] !== "i") {
            $this->_redirector->gotoSimple($this->referer['action'],
                                           $this->referer['controller'],
                                           $this->referer['module'],
                                           $this->referer); // Επιστρέφουμε από εκεί που ήρθαμε
        } else {
            $this->_redirector->gotoSimple('index');
        }
    }

    public function indexAction() {
        $this->view->form = $this->getForm();
    }
}

?>