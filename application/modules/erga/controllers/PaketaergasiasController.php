<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_PaketaergasiasController extends Zend_Controller_Action {
    public function postDispatch() {
        $this->view->pageTitle = "Διαχείριση Παραδοτέων - ".$this->view->getProject();
        if($this->getRequest()->getParam('workpackageid') != null || $this->getRequest()->getParam('deliverableid') != null) {
            $subproject = $this->view->getSubProject();
            $this->view->pageTitle .= '<BR>Υποέργο '.$subproject->get_subprojectnumber();
        }
    }

    public function indexAction() {
        if($this->getRequest()->getParam('projectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', 'Diaxeirisi', $this->_request->getModuleName());
        }
        $this->view->project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        $this->view->type = "subprojects";
        if($this->view->project != null && $this->view->project->get_iscomplex() == 0) {
            $this->view->subproject = $this->view->project->getVirtualSubProject();
            $this->view->type = "deliverables";
            $this->_helper->viewRenderer('indexsimple');
        }
    }

    public function newAction() {
        if($this->getRequest()->getParam('subprojectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        $this->view->subproject = $subproject;
        if($subproject == null) {
            throw new Exception("Το συγκεκριμένο υποέργο δεν υπάρχει.");
        }
        /*if($subproject->get_parentproject()->get_iscomplex() == 0) {
            throw new Exception('Δεν είναι δυνατή η δημιουργία πακέτων εργασίας σε απλά έργα.');
        }*/

        $workPackage = new Erga_Model_SubItems_WorkPackage();
        $workPackage->set_subproject($subproject);
        $form = new Erga_Form_Ypoerga_PaketoErgasias();
        $form->populate($workPackage);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $workPackage->setOptions($form->getValues());
                if($workPackage->get_subproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $workPackage->save();
                if($this->getRequest()->getPost('submitcontinue') != null) {
                    $this->_helper->redirector($this->_request->getActionName(), $this->_request->getControllerName(), $this->_request->getModuleName(), $this->_request->getUserParams());
                } else {
                    $this->_helper->flashMessenger->addMessage('Το πακέτο εργασίας καταχωρήθηκε με επιτυχία.');
                    $this->_helper->redirector('index', 'Paketaergasias', 'erga', array('projectid' => $this->view->getProjectId()));
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
        if($this->getRequest()->getParam('workpackageid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το πακέτο εργασίας υπάρχει
        $workpackage = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_WorkPackage')->find($this->getRequest()->getParam('workpackageid', null));
        $this->view->workpackage = $workpackage;
        if($workpackage == null) {
            throw new Exception("Το συγκεκριμένο πακέτο εργασίας δεν υπάρχει.");
        }
        /*if($workpackage->get_subproject()->get_parentproject()->get_iscomplex() == 0) {
            throw new Exception('Δεν είναι δυνατή η επεξεργασία πακέτων εργασίας σε απλά έργα.');
        }*/

        $form = new Erga_Form_Ypoerga_PaketoErgasias();
        $form->populate($workpackage);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $workpackage->setOptions($form->getValues());
                if($workpackage->get_subproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $workpackage->save();
                /*if($workpackage->get_subproject()->get_parentproject()->get_iscomplex() == 0) {
                    $this->_helper->viewRenderer('delreviewconfirm');
                } else {*/
                    $this->_helper->flashMessenger->addMessage('Το πακέτο εργασίας αναθεωρήθηκε με επιτυχία.');
                    $this->_helper->redirector('index', 'Paketaergasias', 'erga', array('projectid' => $this->view->getProjectId()));
                //}
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function deleteAction() {
        return $this->_helper->deleteHelper($this, 'workpackageid', 'Erga_Model_SubItems_WorkPackage', 'workpackage', false);
    }
    
    public function newdeliverableAction() {
        if($this->getRequest()->getParam('workpackageid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $workpackage = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_WorkPackage')->find($this->getRequest()->getParam('workpackageid', null));
        $this->view->workpackage = $workpackage;
        if($workpackage == null) {
            throw new Exception("Το συγκεκριμένο πακέτο εργασίας δεν υπάρχει.");
        }

        $deliverable = new Erga_Model_SubItems_Deliverable();
        $deliverable->set_workpackage($workpackage);
        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
        $form->populate($deliverable);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $deliverable->setOptions($form->getValues());
                if($deliverable->get_workpackage() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό πακέτο εργασίας.');
                }
                $deliverable->save();
                if($this->getRequest()->getPost('submitcontinue') != null) {
                    $this->_helper->redirector($this->_request->getActionName(), $this->_request->getControllerName(), $this->_request->getModuleName(), $this->_request->getUserParams());
                } else {
                    $this->_helper->flashMessenger->addMessage('Το παραδοτέο καταχωρήθηκε με επιτυχία.');
                    $this->_helper->redirector('index', 'Paketaergasias', 'erga', array('projectid' => $this->view->getProjectId()));
                }
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        $this->_helper->viewRenderer('deliverables/new');
        return;
    }

    public function reviewdeliverableAction() {
        if($this->getRequest()->getParam('deliverableid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το πακέτο εργασίας υπάρχει
        $deliverable = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_Deliverable')->find($this->getRequest()->getParam('deliverableid', null));
        $this->view->deliverable = $deliverable;
        if($deliverable == null) {
            throw new Exception("Το συγκεκριμένο παραδοτέο δεν υπάρχει.");
        }

        $form = new Erga_Form_Ypoerga_Paradoteo($this->view);
        $form->populate($deliverable);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $deliverable->setOptions($form->getValues());
                if($deliverable->get_workpackage() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό πακέτο εργασίας.');
                }
                $deliverable->save();
                $this->_helper->flashMessenger->addMessage('Το παραδοτέο αναθεωρήθηκε με επιτυχία.');
                $this->_helper->redirector('index', 'Paketaergasias', 'erga', array('projectid' => $this->view->getProjectId()));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        $this->_helper->viewRenderer('deliverables/review');
        return;
    }

    public function deletedeliverableAction() {
        return $this->_helper->deleteHelper($this, 'deliverableid', 'Erga_Model_SubItems_Deliverable', 'deliverable');
    }
    
    public function importdeliverablesAction() {
        if($this->getRequest()->getParam('workpackageid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $workpackage = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_WorkPackage')->find($this->getRequest()->getParam('workpackageid', null));
        $this->view->workpackage = $workpackage;
        if($workpackage == null) {
            throw new Exception("Το συγκεκριμένο πακέτο εργασίας δεν υπάρχει.");
        }

        $form = new Erga_Form_Ypoerga_ParadoteoImport($this->view);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) { // Αυτό αφορά την UPLOAD form
                $form->getSubForm('default')->getElement('file')->receive();
                $filepath = $form->getSubForm('default')->getElement('file')->getFileName();
                $objectvalues = $this->_helper->importExcel($this, $filepath, 'Erga_Form_Ypoerga_Paradoteo');
                if($filepath != '') {
                    unlink($filepath);
                }
                if(!isset($objectvalues['error'])) {
                    $k = 0;
                    $objects = array();
                    foreach($objectvalues as $curObjectValues) {
                        $objects[$k] = new Erga_Model_SubItems_Deliverable();
                        $objects[$k]->set_workpackage($workpackage);
                        $objects[$k]->setOptions($curObjectValues);
                        if($objects[$k]->get_workpackage() == null) {
                            throw new Exception('Δεν έχει επιλεχθεί πατρικό πακέτο εργασίας.');
                        }
                        $objects[$k]->save();
                        $k++;
                    }
                    $this->view->objects = $objects;
                    $this->_helper->viewRenderer('deliverables/importconfirm');
                } else {
                    $this->view->error = $objectvalues;
                    $this->_helper->viewRenderer('deliverables/importfail');
                }
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->_helper->viewRenderer('deliverables/import');
        return;
    }
}

?>