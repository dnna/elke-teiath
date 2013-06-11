<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_Professor_FyllaController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Στατιστικά Μηνιαίων Φύλλων Παρακολούθησης (Απασχολούμενοι)";
    }

    public function indexAction() {
        $this->view->filters = $this->_helper->filterHelper($this, 'Anafores_Form_TimesheetsFilters');
        $auth = Zend_Auth::getInstance();
        $this->view->filters['supervisoruserid'] = $auth->getStorage()->read()->get_userid();
        $qb = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getEmployeesAggregateQb($this->view->filters);
        $this->view->employees = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->employees->setCurrentPageNumber($this->_helper->getPageNumber($this));
    }

    public function overviewAction() {
        $this->_helper->layout->disableLayout();
        $employee = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->findEmployees(array('afm' => $this->_request->getUserParam('afm')));
        if(!isset($employee) || count($employee) < 1) {
            throw new Exception('Ο απασχολούμενος δεν βρέθηκε.');
        }
        $auth = Zend_Auth::getInstance();
        $this->view->employee = $employee[0];
        $this->view->totalhours = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHoursAndPaidAmount(array(
            'afm'   =>  $employee[0]->get_afm(),
            'year'  =>  $this->_request->getParam('year'),
            'supervisoruserid'  => $auth->getStorage()->read()->get_userid(),
        ));
        $this->view->monthlyhours = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHoursAndPaidAmount(array(
                'afm'   =>  $employee[0]->get_afm(),
                'year'  =>  $this->_request->getParam('year'),
                'supervisoruserid'  => $auth->getStorage()->read()->get_userid(),
            ), 'month');
        $this->view->projecthours = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getHoursAndPaidAmount(array(
                'afm'   =>  $employee[0]->get_afm(),
                'year'  =>  $this->_request->getParam('year'),
                'supervisoruserid'  => $auth->getStorage()->read()->get_userid(),
                'hydrate'   => Doctrine\ORM\Query::HYDRATE_OBJECT,
            ), 'project');
    }

    public function aggregatemfpAction() {
        $this->_helper->layout->disableLayout();
        $employee = Zend_Registry::get('entityManager')->getRepository('Application_Model_Employee')->findEmployees(array('afm' => $this->_request->getUserParam('afm')));
        if(!isset($employee) || count($employee) < 1) {
            throw new Exception('Ο απασχολούμενος δεν βρέθηκε.');
        }
        $this->_helper->createExcelAggregate($this, $employee[0], $this->_request->getParam('year'), $this->_request->getParam('type', 'schedule'), 'aggregatemfp.xlsx');
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        $auth = Zend_Auth::getInstance();
        $filters['supervisoruserid'] = $auth->getStorage()->read()->get_userid();
        // Δεν θέλουμε pagination εδώ
        $employees = Zend_Registry::get('entityManager')->getRepository('Timesheets_Model_Timesheet')->getEmployeesAggregate($filters);

        // Headers
        $headers = array('Ονοματεπώνυμο', 'Δεδουλευμένες Ώρες', 'Συνολική Αμοιβή');

        // Data
        $data = array();
        foreach($employees as $employee) {
            $data[] = array($employee[0]->get_name(), $employee['hours'],
                Zend_Locale_Format::toNumber($employee['paidamount'],array('precision' => 2,'locale' => Zend_Registry::get('Zend_Locale'))));
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_anafora_apasxoloumenwn.xlsx');
    }
}

?>