<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_DiagonismoiController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Διαχείριση Διαγωνισμών";
    }

    public function indexAction() {
        $filters = $this->_helper->filterHelper($this, 'Praktika_Form_Competition_Filters');
        $qb = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_Competition')->findCompetitionsQb($filters);
        $this->view->competitions = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->competitions->setCurrentPageNumber($this->_helper->getPageNumber($this));
    }

    public function newAction() {
        // Έλεγχος ότι η αίτηση υπάρχει
        $competition = new Praktika_Model_Competition();
        $competition->set_type($this->_request->getParam('type'));
        $this->view->committee = $competition;
        if($competition == null) {
            throw new Exception("Ο συγκεκριμένος διαγωνισμός δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($competition);
        }

        $form = new Praktika_Form_Competition($this->view);
        //$form->populate($committee);
        //$form->getSubForm('default')->getElement('active')->setValue(1);

        $this->formhandling($competition, $form);
    }

    public function reviewAction() {
        // Έλεγχος ότι η αίτηση υπάρχει
        $competition = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_Competition')->find($this->getRequest()->getParam('id', null));
        $this->view->pageTitle = "Διαχείριση Διαγωνισμών - ".$competition->get_project();
        $this->view->committee = $competition;
        if($competition == null) {
            throw new Exception("Ο συγκεκριμένος διαγωνισμός δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($competition);
        }

        $form = new Praktika_Form_Competition($this->view);
        $form->populate($competition);

        $this->formhandling($competition, $form);
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        // Δεν θέλουμε pagination εδώ
        $competitions = Zend_Registry::get('entityManager')->getRepository('Praktika_Model_Competition')->findCompetitions($filters);

        // Headers
        $headers = array('Τίτλος Υποέργου', 'Τύπος', 'Στάδιο');
        
        // Data
        $data = array();
        foreach($competitions as $competition) {
            $data[] = array($competition->get_subproject()->get_subprojecttitle(),
                constant(get_class($competition).'::COMPETITIONTYPE_'.$competition->get_competitiontype()),
                $this->view->getCompetitionStageText($competition));
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_anafora_anadoxwn.xlsx');
    }

    protected function formhandling($competition, $form) {
        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $competition->setOptions($form->getValues());
                $competition->save();
                $this->_helper->flashMessenger->addMessage('Οι αλλαγές καταχωρήθηκαν με επιτυχία');
                $this->_helper->redirector('index');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        $this->_helper->viewRenderer('review');
        return;
    }
}

?>