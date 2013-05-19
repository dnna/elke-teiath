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
            'userid' => $authuser->get_userid(),
            'includeEduProject' => true,
        ));
    }

    public function downloadtemplateAction() {
        // Προσθήκη του απασχολούμενου στο εκπαιδευτικό έργο αν είναι καθηγητής και δεν έχει προστεθεί ήδη
        $authuser = clone Zend_Auth::getInstance()->getStorage()->read();
        if(!$this->userInEduProject($authuser)) {
            $this->createEduContract($authuser);
        }

        $this->_helper->layout->disableLayout();
        $form = new Timesheets_Form_TemplateSelect($this->view, true);
        if($this->_request->getParam('month') != null) {
            if($form->isValid($this->_request->getParams())) {
                $timesheet = new Timesheets_Model_Timesheet();
                $timesheet->setOptions($form->getValues());
                $employee = $timesheet->get_employee();
                if($employee->get_project() != null) {
                    $timesheet->set_project($timesheet->get_employee()->get_project());
                } else if($employee->get_subproject() != null) {
                    $timesheet->set_project($timesheet->get_employee()->get_subproject()->get_parentproject());
                } else {
                    throw new Exception('Η συγκεκριμένη σύμβαση δεν έχει συνδεθεί ούτε με έργο ούτε με υποέργο!');
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
                        // Αν το φύλλο αφορά το εκπαιδευτικό έργο τότε το εγκρίνουμε αυτόματα
                        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
                        if($timesheet->get_project()->get_projectid() == $options['project']['educational']) {
                            $timesheet->set_approved(Timesheets_Model_Timesheet::APPROVED);
                        }
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

    protected function userInEduProject(Application_Model_User $user) {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        foreach($user->get_contracts() as $curContract) {
            if($curContract->get_project() != null && $curContract->get_project()->get_projectid() == $options['project']['educational']) {
                return true;
            }
        }
        return false;
    }

    protected function createEduContract(Application_Model_User $user) {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($options['project']['educational']);
        $category = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_EmployeeCategory')->find('A1');
        $specialty = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_EmployeeSpecialty')->find('B4');
        // Get the deliverable
        $subprojects = $project->get_subprojects();
        $workpackages = $subprojects[0]->get_workpackages();
        $deliverables = $workpackages[0]->get_deliverables();
        $deliverable = $deliverables[0];
        // End get the deliverable
        // Find the employee entity
        $contracts = $user->get_contracts();
        if(count($contracts) > 0) {
            $employee = $contracts[0]->get_employee();
        } else {
            throw new Exception('Ο χρήστης πρέπει να απασχολείται σε τουλάχιστον 1 έργο για να χρησιμοποιήσει τα φύλλα χρονοχρέωσης');
        }
        // End find the employee entity
        $semployee = new Erga_Model_SubItems_SubProjectEmployee();
        $semployee->set_project($project);
        $semployee->set_employee($employee);
        $semployee->set_startdate(new EDateTime('-1000 years'));
        $semployee->set_enddate(new EDateTime('+1000 years'));
        $semployee->set_category($category);
        $semployee->set_specialty($specialty);
        $semployee->set_comments('');
        $author = new Erga_Model_SubItems_Author();
        $author->set_deliverable($deliverable);
        $author->set_employee($semployee);

        $em = Zend_Registry::get('entityManager');
        $em->persist($semployee);
        $em->persist($author);
        $em->flush();
    }
}
?>