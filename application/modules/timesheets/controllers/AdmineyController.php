<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_AdmineyController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Διαχείρηση Φύλλων (Επιστημονικά Υπεύθυνος)";
    }

    public function indexAction() {
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        $filters = $this->_helper->filterHelper($this, 'Timesheets_Form_TimesheetFilters');
        $entries = array();
        $entries = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->findTimesheets($filters+array(
            'supervisoruserid' => $authuser->get_userid()
        ));
        $this->view->entries = $entries;
    }

    public function downloadtemplateAction() {
        $this->_helper->layout->disableLayout();
        $form = new Timesheets_Form_TemplateSelect($this->view);
        if($this->_request->getParam('month') != null) {
            if($form->isValid($this->_request->getParams())) {
                $timesheet = new Timesheets_Model_Timesheet();
                $timesheet->setOptions($form->getValues());
                $authuser = Zend_Auth::getInstance()->getStorage()->read();
                if(isset($authuser) && $timesheet->isSupervisor($authuser)) {
                    // Δεν κάνουμε τίποτα
                } else {
                    throw new Exception('Απαγορεύεται η πρόσβαση');
                }
                $this->_helper->createExcelTimesheet($this, $timesheet, 'mfp_mis'.$timesheet->get_project()->get_basicdetails()->get_mis().'_afm'.$timesheet->get_employee()->get_employee()->get_afm().'_'.$timesheet->get_month().'_'.$timesheet->get_year().'.xlsx');
            }
        }
        $this->view->tplselectform = $form;
    }

    public function exportAction() {
        $timesheet = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->find($this->_request->getParam('timesheetid'));
        if(!isset($timesheet)) {
            throw new Exception('Το συγκεκριμένο φύλλο παρακολούθησης δεν βρέθηκε.');
        }
        // Ελέγχουμε αν ο χρήστης έχει πρόσβαση να εξάγει φύλλα για το συγκεκριμένο έργο
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        if(isset($authuser) && $timesheet->isSupervisor($authuser)) {
            $this->_helper->createExcelTimesheet($this, $timesheet, 'mfp_mis'.$timesheet->get_project()->get_basicdetails()->get_mis().'_afm'.$timesheet->get_employee()->get_employee()->get_afm().'_'.$timesheet->get_month().'_'.$timesheet->get_year().'.xlsx');
        } else {
            throw new Exception('Δεν έχετε πρόσβαση να εξάγετε μηνιαία φύλλα παρακολούθησης για το συγκεκριμένο έργο.');
        }
    }

    public function importAction() {
        $form = new Erga_Form_Ypoerga_ParadoteoImport($this->view);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) { // Αυτό αφορά την UPLOAD form
                $form->getSubForm('default')->getElement('file')->receive();
                $filepath = $form->getSubForm('default')->getElement('file')->getFileName();
                $timesheet = $this->_helper->importExcelTimesheet($this, $filepath);
                if($filepath != '') {
                    unlink($filepath);
                }
                if(!is_array($timesheet) || !isset($timesheet['error'])) {
                    // Ελέγχουμε αν ο χρήστης έχει πρόσβαση να προσθέσει φύλλα για αυτό το έργο (πρέπει να είναι ΕΛΚΕ ή ο επιστημονικά υπεύθυνος του έργου)
                    $authuser = Zend_Auth::getInstance()->getStorage()->read();
                    if(isset($authuser) && $timesheet->isSupervisor($authuser)) {
                        $this->_helper->checkTimesheetValidity($timesheet);
                        $timesheet->save();
                        $this->_helper->flashMessenger->addMessage('Το μηνιαίο φύλλο παρακολούθησης εισήχθη με επιτυχία.');
                        $this->_helper->redirector('index');
                    } else {
                        throw new Exception('Δεν έχετε πρόσβαση να προσθέσετε μηνιαία φύλλα παρακολούθησης για το συγκεκριμένο έργο.');
                    }
                } else {
                    $this->view->error = $timesheet;
                    $this->_helper->viewRenderer('importfail');
                }
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        return;
    }

    public function changeapprovalAction() {
        // Έλεγχος ότι το φύλλο υπάρχει
        $timesheet = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->find($this->getRequest()->getParam('id', null));
        // Έλεγχος πρόσβασης
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        if(!isset($authuser) || !$timesheet->isSupervisor($authuser)) {
            throw new Exception('Δεν έχετε πρόσβαση να αλλάξετε την κατάσταση έγκρισης φύλλων χρονοχρέωσης για το συγκεκριμένο έργο.');
        }
        $this->view->timesheet = $timesheet;
        if($timesheet == null) {
            throw new Exception("Το συγκεκριμένο φύλλο χρονοχρέωσης δεν υπάρχει.");
        }
        $form = new Timesheets_Form_ChangeApproval($this->view);
        $form->populate($timesheet);
        $form->getSubForm('default')->getElement('projecttitle')->setValue($timesheet->get_project()->__toString());
        $form->getSubForm('default')->getElement('employeename')->setValue($timesheet->get_employee());
        $form->getSubForm('default')->getElement('monthyear')->setValue($timesheet->get_month().'/'.$timesheet->get_year());

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $timesheet->setOptions($form->getValues());
                $timesheet->save();
                $this->_helper->flashMessenger->addMessage('Οι αλλαγές καταχωρήθηκαν με επιτυχία');
                $this->_helper->redirector('index');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $subform = $form->getSubForm('default');
        $workinghours = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHours(array(
            'afm'   =>  $timesheet->get_employee()->get_employee()->get_afm(),
            'year'  =>  $timesheet->get_year(),
        ));
        $subform->getElement('hoursbefore')->setValue($workinghours[0]['hours']);
        if($timesheet->get_approved() == Timesheets_Model_Timesheet::APPROVED) {
            $subform->getElement('hoursafter')->setValue($subform->getElement('hoursbefore')->getValue());
        } else {
            $subform->getElement('hoursafter')->setValue($subform->getElement('hoursbefore')->getValue() + $timesheet->getTotalHours());
        }
        $subform->getElement('hoursallowed')->setValue($timesheet->get_employee()->get_employee()->get_maxhours());
        $this->view->timesheet = $timesheet;
        $this->view->hoursbefore = $subform->getElement('hoursbefore')->getValue();
        $this->view->hoursafter = $subform->getElement('hoursafter')->getValue();
        $this->view->maxhours = $timesheet->get_employee()->get_employee()->get_maxhours();
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function deleteAction() {
        $timesheet = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->find($this->getRequest()->getParam('id', null));
        // Έλεγχος πρόσβασης
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        if(!isset($authuser) || !$timesheet->isSupervisor($authuser)) {
            throw new Exception('Δεν έχετε πρόσβαση να αλλάξετε την κατάσταση έγκρισης φύλλων χρονοχρέωσης για το συγκεκριμένο έργο.');
        }
        // Διαγραφή
        if($this->view->userCanDelete($timesheet, true) == false) {
            $this->getHelper('flashMessenger')->addMessage(array('error' => 'Το συγκεκριμένο φύλλο δεν μπόρεσε να διαγραφεί.'));
            $this->getHelper('redirector')->gotoUrlAndExit(urldecode($this->_request->getUserParam('return'))); // Επιστρέφουμε από εκεί που ήρθαμε*/
            return;
        } else {
            return $this->_helper->deleteHelper($this, 'id', 'Timesheets_Model_Timesheet', 'timesheet');
        }
    }
}
?>