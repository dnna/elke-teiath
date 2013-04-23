<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_ApasxoloumenoiController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Αναφορές Απασχολούμενων";
    }

    public function indexAction() {
        $this->view->filters = $this->_helper->filterHelper($this, 'Anafores_Form_ApasxoloumenoiFilters');
        $qb = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->getEmployeesAggregateQb($this->view->filters);
        $this->view->employees = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->employees->setCurrentPageNumber($this->_helper->getPageNumber($this));
    }

    public function overviewAction() {
        $this->_helper->layout->disableLayout();
        $employee = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->findEmployees(array('afm' => $this->_request->getUserParam('afm')));
        if(!isset($employee) || count($employee) < 1) {
            throw new Exception('Ο απασχολούμενος δεν βρέθηκε.');
        }
        $overview = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->getOverview($employee[0], $this->_request->getParams());
        $this->view->employee = $overview['employee'];
        $this->view->symvaseis = $overview['symvaseis'];
        usort($this->view->symvaseis, array("Erga_Model_SubItems_SubProjectEmployee", "compareEmployees"));
        $this->view->deliverables = $overview['deliverables'];
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        // Δεν θέλουμε pagination εδώ
        $employees = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectEmployee')->getEmployeesAggregate($filters);

        // Headers
        $headers = array('Ονοματεπώνυμο', 'Αριθμός Έργων', 'Συνολική Αμοιβή');

        // Data
        $data = array();
        foreach($employees as $employee) {
            $data[] = array($employee[0]->get_employee()->get_name(), $employee['projectscount'],
                Zend_Locale_Format::toNumber($employee['totalamount'],array('precision' => 2,'locale' => Zend_Registry::get('Zend_Locale'))));
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_anafora_apasxoloumenwn.xlsx');
    }
}

?>