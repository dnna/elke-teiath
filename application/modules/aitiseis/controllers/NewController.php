<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_NewController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Νέα Αίτηση";
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        $this->view->type = $this->_helper->getMapping($this->_request->getUserParam('type', 'ypovoliergou'));
        $type = $this->view->type;
        $this->view->pageTitle = "Νέα ".$type::type;;
    }

    public function postDispatch() {
        if(isset($this->view->type)) {
            $this->view->reversetype = $this->_helper->getReverseMapping($this->view->type);
        } else {
            $this->view->reversetype = 'ypovoliaitimatos';
        }
    }

    public function indexAction() {
        $aitisi = new $this->view->type();
        $formclass = $aitisi::formclass;
        $form = new $formclass($aitisi, $this->view);
        $form->populate($aitisi);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                //$aitisi->save(); // Δημιουργία κατάλληλων ids για να υπάρχουν τα relations
                $aitisi->setOptions($form->getValues());
                $aitisi->save();
                $this->_helper->flashMessenger->addMessage('Η αίτηση προστέθηκε με επιτυχία');
                $this->_helper->redirector('index', 'view', null, array('type' => $this->_request->getParam('type')));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->aitisi = $aitisi;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }
}

?>