<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_SynedriaseiseeController extends Api_IndexController
{
    const name = 'Γεγονότα Ημερολογίου ΕΕΕ';

    public function init() {
        $this->_allowAnonymous = true;
        parent::init();
    }
    
    // Χρησιμοποιείται και για το export σε μορφή iCalendar
    public static function getEvents(&$filters = array()) {
        if(!isset($filters['start'])) {
            $start = new EDateTime('-1 year');
            $filters['start'] = $start->getTimestamp();
        }
        if(!isset($filters['end'])) {
            $end = new EDateTime('+1 year');
            $filters['end'] = $end->getTimestamp();
        }
        $synedriaseis = Zend_Registry::get('entityManager')
                        ->getRepository('Synedriaseisee_Model_Synedriasi')
                        ->findSynedriaseis($filters);
        $competitionevents = Zend_Registry::get('entityManager')
                        ->getRepository('Praktika_Model_Competition')
                        ->findCompetitionEvents($filters);
        return array_merge($synedriaseis, $competitionevents);
    }

    public function indexAction() {
        $this->view->filters = $this->getRequest()->getParams();
        $this->view->events = self::getEvents($this->view->filters);

        if($this->_request->getParam('timestamps') != null && $this->_request->getParam('timestamps') === 'true') {
            $this->view->timestamps = true;
        } else if($this->_request->getParam('iso8601') != null && $this->_request->getParam('iso8601') === 'true') {
            $this->view->iso8601 = true;
        }
        if($this->_request->getParam('nowrap') != null && $this->_request->getParam('nowrap') === 'true') {
            $this->view->nowrap = true;
        }
    }

    public function getAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $object = Zend_Registry::get('entityManager')->getRepository('Synedriaseisee_Model_Synedriasi')->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Η συνεδρίαση δεν βρέθηκε.', 404);
        }
        $form = new Synedriaseisee_Form_Synedriasi($this->view);
        $this->_helper->Get($this, $object, $form, 'synedriasi');
    }

    public function postAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $form = new Synedriaseisee_Form_Synedriasi($this->view);
        $this->_helper->PostOrPut($this, 'Synedriaseisee_Model_Synedriasi', $form);
    }

    public function putAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $form = new Synedriaseisee_Form_Synedriasi($this->view);
        $form->setRequired(false);
        $this->_helper->PostOrPut($this, 'Synedriaseisee_Model_Synedriasi', $form, $this->_request->getParam('id'));
    }

    public function deleteAction() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || !$auth->getStorage()->read()->hasRole('elke')) {
            throw new Exception('Access denied');
        }
        $this->_helper->Delete($this, 'Synedriaseisee_Model_Synedriasi', $this->_request->getParam('id'));
    }

    public function schemaAction() {
        $this->_helper->viewRenderer->setNoRender(TRUE);
        echo $this->_helper->generateXsd($this, new Synedriaseisee_Form_Synedriasi($this->view));
    }
}
?>