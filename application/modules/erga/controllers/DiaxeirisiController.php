<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_DiaxeirisiController extends Zend_Controller_Action {

    public function postDispatch() {
        $this->view->pageTitle = "Διαχείριση Έργων";
        $project = $this->view->getProject();
        if(isset($project)) {
            $this->view->pageTitle .= " - ".$project;
        }
    }

    public function indexAction() {
        $filters = $this->_helper->filterHelper($this, 'Erga_Form_ErgaFilters');
        $qb = Zend_Registry::get('entityManager')
                ->getRepository('Erga_Model_Project')
                ->findProjectsQb($filters);
        $this->view->entries = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->entries->setCurrentPageNumber($this->_helper->getPageNumber($this));
        if(isset($filters['showcompletes'])) { $this->view->showcompletes = $filters['showcompletes']; }
        if(isset($filters['showoverdues'])) { $this->view->showoverdues = $filters['showoverdues']; }
        $this->view->type = "projects";
    }

    public function newAction() {
        $form = new Erga_Form_Project($this->getRequest()->getParam('section', 'basicdetails'), $this->view);
        $project = new Erga_Model_Project();
        $form->populate($project);
        //$form->populate(Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find(18)); // DEBUG

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.

                $project->simplySave();
                $form->getSubForm('basicdetails')->getElement('basicdetailsid')->setValue($project->get_basicdetails()->get_basicdetailsid());
                $form->getSubForm('position')->getElement('positionid')->setValue($project->get_position()->get_positionid());

                $values = $form->getValues();
                if($values['position']['default']['teirole'] == 0 || $values['position']['default']['teirole'] == 1 || $values['position']['default']['teirole'] == 2) {
                    unset($values['position']['anadoxos']);
                }
                unset($values['projectid']);
                $project->setOptions($values);
                $project->save();
                $this->view->project = $project;
                $this->_helper->viewRenderer('newconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        return;
    }

    public function reviewAction() {
        if($this->getRequest()->getParam('section') == null) {
            $this->getRequest()->setParam('section', 'basicdetails');
        }
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        $this->view->project = $project;
        $form = new Erga_Form_Project($this->getRequest()->getParam('section'), $this->view, $project);
        $form->populate($project);

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost())) {
                // Η φόρμα ΕΙΝΑΙ έγκυρη. Αποθηκεύουμε στη βάση και στέλνουμε το χρήστη στη σελίδα επιβεβαίωσης.
                $values = $form->getValues();
                if(isset($values['position'])) {
                    if($values['position']['default']['teirole'] == 0 || $values['position']['default']['teirole'] == 1 || $values['position']['default']['teirole'] == 2) {
                        if($project->get_position() != null && $project->get_position()->get_anadoxos() != null) { // Bug fix για να διαγράφεται σωστά ο ανάδοχος
                            $project->get_position()->set_anadoxos(null);
                        }
                        unset($values['position']['anadoxos']);
                    }
                }
                $project->setOptions($values);
                $project->save();
                $this->_helper->viewRenderer('reviewconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->form = $form;
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/formchangedwarning.js', 'text/javascript'));
        
        $section = $this->getRequest()->getParam('section');
        if($section === 'basicdetails') {
            $this->_helper->viewRenderer('review/basicdetails');
        } else if($section === 'financialdetails') {
            $this->_helper->viewRenderer('review/financialdetails');
        } else if($section === 'position') {
            $this->_helper->viewRenderer('review/position');
        } else if($section === 'aitiseis') {
            $this->_helper->viewRenderer('review/aitiseis');
        }
        return;
    }

    public function deleteAction() {
        if($this->getRequest()->getParam('projectid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            $this->_helper->redirector('index', $this->_request->getControllerName());
        }
        // Έλεγχος ότι το ιδρυματικό project υπάρχει
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        if($project == null) {
            throw new Exception("Το συγκεκριμένο έργο δεν υπάρχει.");
        }

        $form = new Application_Form_DeleteForm();

        if ($this->getRequest()->isPost()) {
            // Η φόρμα έχει σταλθεί. Ελέγχουμε αν ειναι έγκυρη.
            if ($form->isValid($this->getRequest()->getPost()) &&
                ($form->getValue('deleteConfirm') === "ΝΑΙ" || $form->getValue('deleteConfirm') === "YES")) {
                $project->remove();
                $this->_helper->viewRenderer('deleteconfirm');
                return;
            }
        }
        // Η φόρμα δεν έχει σταλθεί ή δεν είναι έγκυρη. Τη στέλνουμε στο view και σταματάμε.
        $this->view->project = $project;
        $this->view->form = $form;
        return;
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        // Δεν θέλουμε pagination εδώ
        $projects = Zend_Registry::get('entityManager')
                                ->getRepository('Erga_Model_Project')
                                ->findProjects($filters);

        // Headers
        $headers = array('MIS', 'Κωδ. Λογιστηρίου', array('Τίτλος', array('width' => 50)), array('Τίτλος (Αγγλικά)', array('width' => 50)),
            'Επιστημονικά Υπεύθυνος', array('Περιγραφή', array('width' => 50)), 'Ημερομηνία Έναρξης', 'Ημερομηνία Λήξης', 'Απόφαση Ένταξης', 'Σχόλια', '-',
            'Προυπολογισμός', 'ΦΠΑ', 'Φορέας Χρηματοδότησης', 'Κατηγορία', 'Ενάριθμος ΣΑΕ', 'Εθνική Συμμετοχή', 'Κοινοτική Συμμετοχή',
            'Πλαίσιο Χρηματοδότησης', 'Επιχειρισιακό Πρόγραμμα', 'Άξονας');
        
        // Data
        $data = array();
        foreach($projects as $project) {
            $data[] = array($project->get_basicdetails()->get_mis(), $project->get_basicdetails()->get_acccode(),
                $project->get_basicdetails()->get_title(), $project->get_basicdetails()->get_titleen(),
                $project->get_basicdetails()->get_supervisor()->get_realnameLowercase(), $project->get_basicdetails()->get_description(),
                $project->get_basicdetails()->get_startdate(), $project->get_basicdetails()->get_enddate(),
                $project->get_basicdetails()->get_refnumstart(), $project->get_basicdetails()->get_comments(), '',
                $project->get_financialdetails()->get_budget(), $project->get_financialdetails()->get_budgetfpa(),
                $project->get_financialdetails()->get_fundingagency()->get_name(), $project->get_financialdetails()->get_category()->get_name(),
                $project->get_financialdetails()->get_sae(), $project->get_financialdetails()->get_nationalparticipation(),
                $project->get_financialdetails()->get_europeanparticipation(), $project->get_financialdetails()->get_fundingframework()->get_fundingframeworkname(),
                $project->get_financialdetails()->get_opprogramme()->get_opprogrammename(), $project->get_financialdetails()->get_axis());
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_projects.xlsx');
    }

    public function overviewAction() {
        $this->_helper->layout->disableLayout();
        $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($this->getRequest()->getParam('projectid', null));
        if(!isset($project) || count($project) < 1) {
            throw new Exception('Το έργο δεν βρέθηκε.');
        }
        $this->view->project = $project;
        $workpackages = array();
        $workpackages['total'] = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHoursAndPaidAmount(array(
            'projectid'   =>  $project->get_projectid(),
        ), 'workpackage');
        $categories = array();
        foreach($project->get_personnelcategories() as $curCategory) {
            $workpackages[$curCategory->get_name()] = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHoursAndPaidAmount(array(
                'projectid'   =>  $project->get_projectid(),
                'personnelcategoryid'   =>  $curCategory->get_recordid(),
            ), 'workpackage');
            $categories[$curCategory->get_name()] = $curCategory;
        }
        $this->view->tcategories = $categories;
        $this->view->tworkpackages = $workpackages;
    }

    public function feedAction() {
        // Βρίσκουμε τα έργα του tokenuser (sorted σε φθήνουσα σειρά με βάση το lastupdatedate)
        $filters = $this->_helper->filterHelper($this, 'Erga_Form_ErgaFilters');
        $qb = Zend_Registry::get('entityManager')
                                ->getRepository('Erga_Model_Project')
                                ->findProjectsQb($filters);
        $qb->orderBy('p._lastupdatedate', 'DESC');
        $projects = $qb->getQuery()->getResult();
        // Δημιουργία των rss entries
        $feed = array();
        $feed['entries'] = array();
        foreach($projects as $curProject) {
            /* @var $curProject Erga_Model_Project */
            $entry = array(); //Container for the entry before we add it on
            $entry['title'] = $curProject->__toString(); //The title that will be displayed for the entry
            $entry['link'] = htmlentities($this->_request->getScheme().'://'.$this->_request->getHttpHost().$this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'review', 'projectid' => $curProject->get_projectid()), null, true)); //The url of the entry
            $entry['description'] = $curProject->get_basicdetails()->get_description(); //Short description of the entry
            //$entry['content'] = $curProject->get_basicdetails()->get_description(); //Long description of the entry
            //Some optional entries, usually the more info you can provide, the better
            $entry['lastUpdate'] = $curProject->get_lastupdatedate()->getTimestamp(); //Unix timestamp of the last modified date
            //$entry['comments'] = $object->commentsUrl; //Url to the comments page of the entry
            //$entry['commentsRss'] = $object->commentsRssUrl; //Url of the comments pages rss feed
            $feed['entries'][] = $entry;
        }
        $this->_helper->createRss($this, $feed);
    }

    public function subprojectsnameAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_request->getParams();
        if(isset($params['projectid']) && $params['projectid'] != null) {
            $project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($params['projectid']);
            $params = $params + $project->getOptions();
        }
        if(!isset($project)) {
            $project = new Erga_Model_Project();
        }
        $genform = new Erga_Form_SubprojectsName($this->view);
        $genform->populate($project);
        $this->view->form = $genform;
        echo $this->view->form;
    }
}

?>