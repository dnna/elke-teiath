<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_ApasxoloumenoiController extends Zend_Controller_Action {
    public function postDispatch() {
        $this->view->pageTitle = "Διαχείριση Απασχολούμενων/Αναδόχων - ".$this->view->getProject();
        if($this->getRequest()->getParam('subprojectid', null) != null) {
            $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        } else if($this->getRequest()->getParam('employeeid', null)) {
            $employee = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($this->getRequest()->getParam('employeeid', null));
            if($employee != null) {
                $subproject = $employee->get_subproject();
            }
        }
        if($subproject != null) {
            $this->view->pageTitle .= '<BR>Υποέργο '.$subproject->get_subprojectnumber();
        }
    }

    public function indexAction() {
        if($this->getRequest()->getParam('projectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί projectid.');
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        $this->view->project = $project;
        if($project == null) {
            throw new Exception("Το συγκεκριμένο έργο δεν υπάρχει.");
        }
        $form = new Erga_Form_PersonnelCategories($this->view);
        $form->populate($project);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $values = $form->getValues();
                $project->setOptions($values);
                $project->save();
                $this->_helper->flashMessenger->addMessage('Οι κατηγορίες προσωπικού ανανεώθηκαν με επιτυχία.');
                $this->_helper->redirector('index', 'Apasxoloumenoi', 'erga', $this->_request->getUserParams());
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
    }

    public function newAction() {
        if($this->getRequest()->getParam('subprojectid') == null && $this->getRequest()->getParam('projectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        $employee = new Erga_Model_SubItems_SubProjectEmployee();
        // Έλεγχος ότι το υποέργο υπάρχει
        if($this->getRequest()->getParam('subprojectid', null) != null) {
            $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        } else if($this->getRequest()->getParam('projectid', null) != null) {
            $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        }
        if(isset($subproject)) {
            if($subproject->get_subprojectdirectlabor() != "1") {
                throw new Exception("Το έργο δεν είναι αυτεπιστασία.");
            }
            $employee->set_subproject($subproject);
            $this->view->subproject = $subproject;
        } else if(isset($project)) {
            $employee->set_project($project);
            $this->view->project = $project;
        }
        $form = new Erga_Form_Apasxoloumenoi_Employee();
        $form->populate($employee);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $employee->setOptions($form->getValues());
                if($employee->get_subproject() == null && $employee->get_project() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $employee->save();
                if($this->getRequest()->getPost('submitcontinue') != null) {
                    $this->_helper->redirector($this->_request->getActionName(), $this->_request->getControllerName(), $this->_request->getModuleName(), $this->_request->getUserParams());
                } else {
                    $this->_helper->flashMessenger->addMessage('Οι απασχολούμενος καταχωρήθηκε με επιτυχία.');
                    $this->_helper->redirector('index', 'Apasxoloumenoi', 'erga', array('projectid' => $this->view->getProjectId()));
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
        if($this->getRequest()->getParam('employeeid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName(), $this->getRequest()->getUserParam());
        }
        // Έλεγχος ότι το πακέτο εργασίας υπάρχει
        $employee = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->find($this->getRequest()->getParam('employeeid', null));
        $this->view->employee = $employee;
        if($employee == null) {
            throw new Exception("Ο συγκεκριμένος απασχολούμενος δεν υπάρχει.");
        }
        if($employee->get_subproject() != null && $employee->get_subproject()->get_subprojectdirectlabor() != "1") {
            throw new Exception("Το έργο δεν είναι αυτεπιστασία.");
        }

        $form = new Erga_Form_Apasxoloumenoi_Employee();
        $form->populate($employee);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $employee->setOptions($form->getValues());
                if($employee->get_subproject() == null && $employee->get_project() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $employee->save();
                $this->_helper->flashMessenger->addMessage('Οι μεταβολές στον απασχολούμενο ολοκληρώθηκαν με επιτυχία.');
                $this->_helper->redirector('index', 'Apasxoloumenoi', 'erga', array('projectid' => $this->view->getProjectId()));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function deleteAction() {
        return $this->_helper->deleteHelper($this, 'employeeid', 'Erga_Model_SubItems_SubProjectEmployee', 'employee');
    }

    public function newcontractorAction() {
        if($this->getRequest()->getParam('subprojectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        $this->view->subproject = $subproject;
        if($subproject == null) {
            throw new Exception("Το συγκεκριμένο υποέργο δεν υπάρχει.");
        }
        if($subproject->get_subprojectdirectlabor() == "1") {
            throw new Exception("Το έργο είναι αυτεπιστασία.");
        }

        $contractor = new Erga_Model_SubItems_SubProjectContractor();
        $contractor->set_subproject($subproject);
        $form = new Erga_Form_Apasxoloumenoi_Contractor();
        $form->populate($contractor);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $contractor->setOptions($form->getValues());
                if($contractor->get_subproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $contractor->save();
                if($this->getRequest()->getPost('submitcontinue') != null) {
                    $this->_helper->redirector($this->_request->getActionName(), $this->_request->getControllerName(), $this->_request->getModuleName(), $this->_request->getUserParams());
                } else {
                    $this->_helper->flashMessenger->addMessage('Ο ανάδοχος καταχωρήθηκε με επιτυχία.');
                    $this->_helper->redirector('index', 'Apasxoloumenoi', 'erga', array('projectid' => $this->view->getProjectId()));
                }
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function reviewcontractorAction() {
        if($this->getRequest()->getParam('contractorid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName(), $this->getRequest()->getUserParam());
        }
        // Έλεγχος ότι το πακέτο εργασίας υπάρχει
        $contractor = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectContractor')->find($this->getRequest()->getParam('contractorid', null));
        $this->view->contractor = $contractor;
        if($contractor == null) {
            throw new Exception("Ο συγκεκριμένος ανάδοχος δεν υπάρχει.");
        }
        if($contractor->get_subproject()->get_subprojectdirectlabor() == "1") {
            throw new Exception("Το έργο είναι αυτεπιστασία.");
        }

        $form = new Erga_Form_Apasxoloumenoi_Contractor();
        $form->populate($contractor);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $contractor->setOptions($form->getValues());
                if($contractor->get_subproject() == null) {
                    throw new Exception('Δεν έχει επιλεχθεί πατρικό υποέργο.');
                }
                $contractor->save();
                $this->_helper->flashMessenger->addMessage('Οι μεταβολές στον ανάδοχο ολοκληρώθηκαν με επιτυχία.');
                $this->_helper->redirector('index', 'Apasxoloumenoi', 'erga', array('projectid' => $this->view->getProjectId()));
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function deletecontractorAction() {
        return $this->_helper->deleteHelper($this, 'contractorid', 'Erga_Model_SubItems_SubProjectContractor', 'contractor');
    }

    public function exportemployeesAction() {
        if($this->getRequest()->getParam('subprojectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί subprojectid.');
        }
        // Έλεγχος ότι το υποέργο υπάρχει
        $subproject = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubProject')->find($this->getRequest()->getParam('subprojectid', null));
        if($subproject == null) {
            throw new Exception("Το συγκεκριμένο υποέργο δεν υπάρχει.");
        }

        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $attachmentName = 'D03-OnomastikiKatastasiApasxoloumenwnYpoergou.'.$options['livedocx']['preferedOutput'];

        // Headers
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(TRUE);
        $attachment = $this->_helper->createDoc($this, $subproject, 'D03-OnomastikiKatastasiApasxoloumenwnYpoergou');
        $this->getResponse()
             ->setHeader('Content-Description', 'File Transfer')
             ->setHeader('Content-Type', $options['livedocx']['mimeType'])
             ->setHeader('Content-Disposition', 'attachment; filename='.$attachmentName)
             ->setHeader('Content-Transfer-Encoding', 'binary')
             ->setHeader('Expires', '0')
             ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
             ->setHeader('Pragma', 'public')
             ->setHeader('Content-Length', $this->_helper->getBinaryDataSize($attachment));
        echo $attachment;
    }
}

?>