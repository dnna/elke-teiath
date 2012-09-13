<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class ReportController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Αναφορά προβλήματος";
    }

    public function preDispatch() {
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        if(!isset($config['report']) || !isset($config['report']['redmineUrl'])) {
            throw new Exception('Ο σύνδεσμος αναφοράς προβλημάτος έχει απενεργοποιηθεί στο configuration της εφαρμογής');
        }
    }

    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        $form = new Application_Form_ReportForm();

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $data = array();
                $data['subject'] = 'Αναφορά Προβλήματος από: '.Zend_Auth::getInstance()->getStorage()->read()->get_userid();
                $data['description'] = $form->getSubForm('default')->getValue('description');
                $result = $this->_helper->createRedmineIssue($this, $data);
                if($result != false) {
                    $this->_helper->viewRenderer('confirm');
                } else {
                    throw new Exception('Δεν μπόρεσε να γίνει αναφορά προβλήματος');
                }
                return;
            }
        }
        $this->view->form = $form;
        return;
    }
}

?>