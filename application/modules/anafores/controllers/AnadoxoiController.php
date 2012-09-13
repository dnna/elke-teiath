<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Anafores_AnadoxoiController extends Zend_Controller_Action {
    public function init() {
        $this->view->pageTitle = "Αναφορές Αναδόχων";
    }

    public function indexAction() {
        $this->view->filters = $this->_helper->filterHelper($this, 'Anafores_Form_ApasxoloumenoiFilters');
        $qb = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectContractor')->getContractorsAggregateQb($this->view->filters);
        $this->view->contractors = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
        $this->view->contractors->setCurrentPageNumber($this->_helper->getPageNumber($this));
    }

    public function overviewAction() {
        $this->_helper->layout->disableLayout();
        $agency = Zend_Registry::get('entityManager')->getRepository('Application_Model_Contractor')->findBy(array('_afm' => $this->_request->getUserParam('afm')));
        if(!isset($agency) || count($agency) < 1) {
            throw new Exception('Ο ανάδοχος δεν βρέθηκε.');
        }
        $overview = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectContractor')->getOverview($agency[0], $this->_request->getParams());
        $this->view->contractor = $overview['contractor'];
        $this->view->subprojects = $overview['subprojects'];
        $this->view->deliverables = $overview['deliverables'];
    }

    public function exportAction() {
        $filters = $this->getRequest()->getParam('filters');
        // Δεν θέλουμε pagination εδώ
        $contractors = Zend_Registry::get('entityManager')->getRepository('Erga_Model_SubItems_SubProjectContractor')->getContractorsAggregate($filters);

        // Headers
        $headers = array('Επωνυμία', 'Αριθμός Έργων', 'Συνολική Αμοιβή');
        
        // Data
        $data = array();
        foreach($contractors as $contractor) {
            $data[] = array($contractor[0]->get_agency()->get_name(), $contractor['projectscount'],
                Zend_Locale_Format::toNumber($contractor['totalamount'],array('precision' => 2,'locale' => Zend_Registry::get('Zend_Locale'))));
        }

        $this->_helper->createExcel($this, $headers, $data, 'elke_anafora_anadoxwn.xlsx');
    }
}

?>