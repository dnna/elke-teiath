<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Timesheets_MytimesheetsController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Τα φύλλα μου";
    }

    public function indexAction() {
        $authuser = Zend_Auth::getInstance()->getStorage()->read();
        $this->view->entries = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->findTimesheets(array(
            'userid' => $authuser->get_userid()
        ));
    }

    public function downloadtemplateAction() {
        $this->_helper->layout->disableLayout();
        $form = new Timesheets_Form_TemplateSelect($this->view, true);
        if($this->_request->getParam('month') != null) {
            if($form->isValid($this->_request->getParams())) {
                $timesheet = new Timesheets_Model_Timesheet();
                $timesheet->setOptions($form->getValues());
                $employee = $timesheet->get_employee();
                // Αν η σύμβαση είναι κένη και ο χρήστης είναι καθηγητής θεωρούμε ότι πρόκειται για το εκπαιδευτικό έργο
                if(!isset($employee)) {
                    $authuser = Zend_Auth::getInstance()->getStorage()->read();
                    $contracts = $authuser->get_contracts();
                    if(count($contracts) > 0) {
                        $authemployee = $contracts[0]->get_employee();
                        $project = Erga_Model_Project::createEducationalProject();
                        $employee = Erga_Model_SubItems_SubProjectEmployee::createEducationalEmployee($authemployee, $timesheet->get_month(), $timesheet->get_year());
                        $timesheet->set_employee($employee);
                    } else {
                        throw new Exception('Ο χρήστης πρέπει να έχει οριστεί σε τουλάχιστον ένα έργο για να χρησιμοποιήσει τα φύλλα χρονοχρέωσης');
                    }
                } else {
                    if($employee->get_project() != null) {
                        $project = $timesheet->get_employee()->get_project();
                        $timesheet->set_project();
                    } else if($employee->get_subproject() != null) {
                        $project = $timesheet->get_employee()->get_subproject()->get_parentproject();
                        $timesheet->set_project($project);
                    } else {
                        throw new Exception('Η συγκεκριμένη σύμβαση δεν έχει συνδεθεί ούτε με έργο ούτε με υποέργο!');
                    }
                }
                $this->_helper->createExcelTimesheet($this, $timesheet, 'mfp_mis'.$project->get_basicdetails()->get_mis().'_afm'.$employee->get_employee()->get_afm().'_'.$timesheet->get_month().'_'.$timesheet->get_year().'.xlsx');
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
        if(isset($authuser) && $authuser->get_userid() === $timesheet->get_employee()->get_employee()->get_ldapusername()) {
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
                    // Ελέγχουμε αν ο χρήστης έχει πρόσβαση να προσθέσει φύλλα για αυτό το έργο (πρέπει να είναι απασχολούμενος στο έργο)
                    $authuser = Zend_Auth::getInstance()->getStorage()->read();
                    if(isset($authuser) && $authuser->get_userid() === $timesheet->get_employee()->get_employee()->get_ldapusername()) {
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

    public function deleteAction() {
        $timesheet = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->find($this->getRequest()->getParam('id', null));
        if($this->view->userCanDelete($timesheet) == false) {
            $this->getHelper('flashMessenger')->addMessage(array('error' => 'Το συγκεκριμένο φύλλο δεν μπόρεσε να διαγραφεί.'));
            $this->getHelper('redirector')->gotoUrlAndExit(urldecode($this->_request->getUserParam('return'))); // Επιστρέφουμε από εκεί που ήρθαμε*/
            return;
        } else {
            return $this->_helper->deleteHelper($this, 'id', 'Timesheets_Model_Timesheet', 'timesheet');
        }
    }
}
?>