<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_YpoergaController extends Zend_Controller_Action {
    public function init() {
        $projectid = $this->getRequest()->getParam('projectid', null);
        if(!isset($projectid)) {
            $projectid = $this->getRequest()->getParam('parentprojectid', null);
        }
        if(isset($projectid)) {
            $this->view->project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($projectid);
            $this->setPageTitle();
        } else {
            $this->view->project = null;
            $name = Erga_Model_Project::getSubProjectNames();
            $name = $name[0];
        }
    }

    public function indexAction() {
        $this->view->type = "subprojects";
        if($this->view->project != null && $this->view->project->get_iscomplex() == 0) {
            $this->_helper->redirector('index', 'Paketaergasias', $this->view->getModuleName(), array('projectid' => $this->view->project->get_projectid()));
        }
    }

    public function newAction() {
        if($this->getRequest()->getParam('parentprojectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το project υπάρχει
        if($this->view->project == null) {
            throw new Exception("Το συγκεκριμένο έργο δεν υπάρχει.");
        }
        if($this->view->project->get_iscomplex() == 0) {
            throw new Exception('Δεν είναι δυνατή η δημιουργία υποέργων σε απλά έργα.');
        }
        
        $subproject = new Erga_Model_SubProject();
        $subproject->set_parentproject($this->view->project);

        $form = new Erga_Form_Ypoerga_Ypoerga($this->view->project->get_basicdetails()->get_supervisor(), $this->view);
        $form->populate($subproject);
        $form->getSubForm('default')->getElement('subprojectnumber')->setValue($this->view->project->getNextSubProjectNumber());

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $subproject->setOptions($form->getValues());
                if($subproject->get_parentproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό έργο.');
                }
                $subproject->save();
                $this->view->subproject = $subproject;
                if($this->getRequest()->getPost('submitcontinue') != null) {
                    $this->_helper->redirector($this->_request->getActionName(), $this->_request->getControllerName(), $this->_request->getModuleName(), $this->_request->getUserParams());
                } else {
                    $this->_helper->viewRenderer('newconfirm');
                }
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function reviewAction() {
        if($this->getRequest()->getParam('subprojectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί subprojectid.');
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        $this->view->subproject = $subproject;
        if($subproject == null) {
            throw new Exception("Το συγκεκριμένο υποέργο δεν υπάρχει.");
        }
        $this->setPageTitle();

        $form = new Erga_Form_Ypoerga_Ypoerga($subproject->get_subprojectsupervisor(), $this->view);
        $form->populate($subproject);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $subproject->setOptions($form->getValues());
                if($subproject->get_parentproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό έργο.');
                } else if($subproject->get_parentproject()->get_iscomplex() == 0) {
                    throw new Exception('Δεν μπορείτε να αλλάξετε τα βασικά στοιχεία στα έργα που δεν είναι σύνθετα.');
                }
                $subproject->save();
                $this->_helper->viewRenderer('reviewconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function deleteAction() {
        return $this->_helper->deleteHelper($this, 'subprojectid', 'Erga_Model_SubProject', 'subproject', false);
    }
    
    private function setPageTitle() {
        $name = $this->view->getProject()->get_subprojectsname();
        $this->view->pageTitle = "Διαχείριση ".$name['genpl']." - ".$this->view->getProject();
    }
}

?>