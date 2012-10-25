<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class ListsController extends Zend_Controller_Action
{
    public function init() {
        $this->view->pageTitle = "Επεξεργασία Λιστών";
    }

    public function indexAction() {
        $this->view->type = $this->getRequest()->getParam('type');
        if(!isset($this->view->type)) {
            $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName(), array('type' => 'Application_Model_Lists_ProjectCategory'));
        }
        if($this->view->type === 'Application_Model_Lists_ProjectCategory') {
            $this->view->typeText = 'Κατηγορίες Έργων';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_ProjectCategory')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_ExpenditureCategory') {
            $this->view->typeText = 'Κατηγορίες Δαπανών';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_ExpenditureCategory')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_Agency') {
            $this->view->typeText = 'Φορείς';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_Agency')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_FundingFramework') {
            $this->view->typeText = 'Πλαίσια Χρηματοδότησης';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_FundingFramework')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_OpProgramme') {
            $this->view->typeText = 'Επιχειρησιακά Προγράμματα';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_OpProgramme')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_EmployeeCategory') {
            $this->view->typeText = 'Κατηγορίες Απασχολούμενων';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_EmployeeCategory')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_EmployeeSpecialty') {
            $this->view->typeText = 'Ειδικότητες Απασχολούμενων';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_EmployeeSpecialty')
                                    ->getList();
        } else if($this->getRequest()->getParam('type') === 'Application_Model_Lists_Bank') {
            $this->view->typeText = 'Τράπεζες';
            $this->view->list = Zend_Registry::get('entityManager')
                                    ->getRepository('Application_Model_Lists_Bank')
                                    ->getList();
        } else {
            throw new Exception('Δεν υπάρχει ο συγκεκριμένος τύπος λίστας.');
        }
        $this->view->pageTitle = "Επεξεργασία Λιστών - ".$this->view->typeText;
    }

    public function addAction() {
       if(!class_exists($this->getRequest()->getParam('type')) || strpos($this->getRequest()->getParam('type'), 'List') === false) {
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }

        $form = new Dnna_Form_AutoForm($this->getRequest()->getParam('type'));

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $this->view->type = $this->getRequest()->getParam('type');
                $item = new $this->view->type();
                try {
                    $item->setOptions($form->getValues());
                    $item->save();
                } catch(PDOException  $e) {
                    $this->_helper->viewRenderer('editfail');
                    return;
                }
                $this->_helper->viewRenderer('editconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        return;
    }

    public function editAction() {
        if(!class_exists($this->getRequest()->getParam('type')) || strpos($this->getRequest()->getParam('type'), 'List') === false) {
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το αντικείμενο υπάρχει
        $item = Zend_Registry::get('entityManager')->getRepository($this->getRequest()->getParam('type'))->find($this->getRequest()->getParam('id'));
        if($item == null) {
            throw new Exception("Το αντικείμενο δεν υπάρχει.");
        }

        $this->view->type = $this->getRequest()->getParam('type');
        $form = new Dnna_Form_AutoForm($this->view->type);
        $form->populate($item);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                try {
                    $item->setOptions($form->getValues());
                    $item->save();
                } catch(PDOException  $e) {
                    $this->_helper->viewRenderer('editfail');
                    return;
                }
                $this->_helper->viewRenderer('editconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        return;
    }

    public function deleteAction() {
        if(!class_exists($this->getRequest()->getParam('type')) || strpos($this->getRequest()->getParam('type'), 'List') === false) {
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        try {
            $this->view->type = $this->getRequest()->getParam('type');
            return $this->_helper->deleteHelper($this, 'id', $this->view->type, 'item');
        } catch(PDOException  $e) {
            $this->_helper->viewRenderer('deletefail');
            return;
        }
    }
}