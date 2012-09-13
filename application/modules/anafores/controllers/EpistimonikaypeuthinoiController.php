<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_EpistimonikaypeuthinoiController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Αναφορές Επιστημονικά Υπευθύνων";
    }

    public function indexAction() {
        $this->view->filters = $this->_helper->filterHelper($this, 'Anafores_Form_EpistimonikaYpeuthinoiFilters');
        $qb = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->getSupervisorsAggregateQb($this->view->filters);
        $this->view->supervisors = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->supervisors->setCurrentPageNumber($this->_helper->getPageNumber($this));
    }

    public function overviewAction() {
        $this->_helper->layout->disableLayout();
        $supervisor = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->findBy(array('_userid' => $this->_request->getUserParam('id')));
        if(!isset($supervisor) || count($supervisor) < 1) {
            throw new Exception('Ο επιστημονικά υπεύθυνος δεν βρέθηκε.');
        }
        $overview = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->getSupervisorOverview($supervisor[0], $this->_request->getParams());
        $this->view->supervisor = $overview['supervisor'];
        $this->view->projects = $overview['projects'];
        $this->view->subprojects = $overview['subprojects'];
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        // Δεν θέλουμε pagination εδώ
        $supervisors = Zend_Registry::get('entityManager')->getRepository('Application_Model_User')->getSupervisorsAggregate($filters);

        // Headers
        $headers = array('Ονοματεπώνυμο', 'Αριθμός Έργων');
        
        // Data
        $data = array();
        foreach($supervisors as $supervisor) {
            $data[] = array($supervisor[0]->get_realnameLowercase(), $supervisor['projectscount']);
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_anafora_anadoxwn.xlsx');
    }
}

?>