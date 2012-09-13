<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class ProfileController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Επεξεργασία Προφίλ";
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $form = new Dnna_Form_AutoForm('Application_Model_User', $this->view);
        $user = Zend_Registry::get('entityManager')
                                ->getRepository('Application_Model_User')
                                ->find($auth->getStorage()->read()->get_userid());
        $form->populate($user);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $user->setOptions($form->getValues());
                $user->save();
                $auth->getStorage()->write($user);
                $this->_helper->viewRenderer('confirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->showMsg = $this->_request->getParam('showMsg');
        $this->view->form = $form;
        return;
    }
}

?>