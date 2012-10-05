<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_ReviewController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Επεξεργασία Αίτησης";
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            $this->_helper->redirector('index', 'Login', 'default');
        }
        $this->view->type = $this->_helper->getMapping($this->_request->getUserParam('type', 'ypovoliergou'));
        $type = $this->view->type;
        $this->view->pageTitle = "Επεξεργασία Αίτησης - ".$type::type;
    }

    public function postDispatch() {
        if(isset($this->view->type)) {
            $this->view->reversetype = $this->_helper->getReverseMapping($this->view->type);
        } else {
            $this->view->reversetype = 'ypovoliaitimatos';
        }
    }

    public function indexAction() {
        // Έλεγχος ότι η αίτηση υπάρχει
        $aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        $this->view->aitisi = $aitisi;
        if($aitisi == null) {
            throw new Exception("Η συγκεκριμένη αίτηση δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($aitisi);
        }
        if($aitisi->get_approved() == Aitiseis_Model_AitisiBase::APPROVED) {
            throw new Exception("Η αίτηση έχει εγκριθεί και πλέον δεν μπορούν να γίνουν αλλαγές σε αυτή.");
        }

        $formclass = $aitisi::formclass;
        $form = new $formclass($aitisi, $this->view);
        $form->populate($aitisi);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $aitisi->setOptions($form->getValues());
                $aitisi->save();
                $this->_helper->flashMessenger->addMessage('Οι αλλαγές καταχωρήθηκαν με επιτυχία');
                $this->_helper->redirector('index', 'view', null, array('type' => $this->_request->getParam('type')));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function changeapprovalAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Ο χρήστης δεν έχει δικαίωμα αλλαγής της κατάστασης έγκρισης των αιτήσεων.');
        }
        // Έλεγχος ότι η αίτηση υπάρχει
        $aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        $this->view->aitisi = $aitisi;
        if($aitisi == null) {
            throw new Exception("Η συγκεκριμένη αίτηση δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($aitisi);
        }
        $form = new Aitiseis_Form_ChangeApproval($aitisi, $this->view);
        $form->populate($aitisi);
        $form->getSubForm('default')->getElement('title')->setValue($aitisi);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $aitisi->setOptions($form->getValues());
                $aitisi->save();
                $this->_helper->flashMessenger->addMessage('Οι αλλαγές καταχωρήθηκαν με επιτυχία');
                $this->_helper->redirector('index', 'view', null, array('type' => $this->_request->getParam('type')));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function exporttoprojectAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Ο χρήστης δεν έχει δικαίωμα δημιουργίας έργου.');
        }
        // Έλεγχος ότι η αίτηση υπάρχει
        $aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        $this->view->aitisi = $aitisi;
        if($aitisi == null) {
            throw new Exception("Η συγκεκριμένη αίτηση δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($aitisi);
        }
        if($aitisi->get_approved() != Aitiseis_Model_AitisiBase::APPROVED) {
            throw new Exception("Η αίτηση δεν έχει εγκριθεί και δεν μπορεί να δημιουργηθεί έργο από αυτή.");
        }
        $this->view->project = $aitisi->exportToProject();
        $this->_helper->flashMessenger->addMessage('Η εξαγωγή σε έργο ολοκληρώθηκε με επιτυχία');
        $this->_helper->redirector('index', 'view', null, array('type' => $this->_request->getParam('type')));
        return;
    }
}

?>