<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Professor_ViewController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Εμφάνιση Έργων Καθηγητή";
    }

    public function indexAction() {
        $filters = $this->_helper->filterHelper($this, 'Erga_Form_ErgaFilters');
        $auth = Zend_Auth::getInstance();
        $filters['supervisoruserid'] = $auth->getStorage()->read()->get_userid();
        $qb = Zend_Registry::get('entityManager')
                ->getRepository('Erga_Model_Project')
                ->findProjectsQb($filters);
        $this->view->entries = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->entries->setCurrentPageNumber($this->_helper->getPageNumber($this));
        if(isset($filters['showcompletes'])) { $this->view->showcompletes = $filters['showcompletes']; }
        if(isset($filters['showoverdues'])) { $this->view->showoverdues = $filters['showoverdues']; }
        $this->view->type = "projects";
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        $auth = Zend_Auth::getInstance();
        $filters['supervisoruserid'] = $auth->getStorage()->read()->get_userid();
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
        $auth = Zend_Auth::getInstance();
        $curuserid = $auth->getStorage()->read()->get_userid();
        if($project->get_basicdetails() == null || $project->get_basicdetails()->get_supervisor() == null || $project->get_basicdetails()->get_supervisor()->get_userid() != $curuserid) {
            throw new Exception('Δεν έχετε πρόσβαση να δείτε πληροφορίες για αυτό το έργο.');
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
        // Βρίσκουμε τα έργα του tokenuser
        $filters = $this->_helper->filterHelper($this, 'Erga_Form_ErgaFilters');
        $filters['supervisoruserid'] = $this->view->tokenuser->get_userid();
        $projects = Zend_Registry::get('entityManager')
                                ->getRepository('Erga_Model_Project')
                                ->findProjects($filters);
        // Δημιουργία των rss entries
        $feed = array();
        $feed['entries'] = array();
        foreach($projects as $curProject) {
            /* @var $curProject Erga_Model_Project */
            $entry = array(); //Container for the entry before we add it on
            $entry['title'] = $curProject->__toString(); //The title that will be displayed for the entry
            $entry['link'] = htmlentities($this->_request->getScheme().'://'.$this->_request->getHttpHost().$this->view->url(array('module' => 'erga', 'controller' => $this->view->getControllerName(), 'action' => 'overview', 'projectid' => $curProject->get_projectid()), null, true)); //The url of the entry
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
}

?>