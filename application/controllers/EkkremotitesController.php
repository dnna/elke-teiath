<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class EkkremotitesController extends Zend_Controller_Action {

    public function init() {
        $this->view->pageTitle = "Εκκρεμότητες";
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance(); // Είναι σίγουρο ότι έχει αυθεντικοποιηθεί αλλιώς δεν θα πέρναγε το acl
        $this->view->user = $auth->getStorage()->read();
    }

    public function indexAction() {
        $this->view->addHelperPath(APPLICATION_PATH.'/modules/aitiseis/views/helpers/');
        $this->view->ekkremeisAitiseis = Zend_Registry::get('entityManager')
                                ->getRepository('Aitiseis_Model_AitisiBase')
                                ->findAitiseis(array(
                                    'approved' => Aitiseis_Model_AitisiBase::PENDING,
                                    'scheduled' => false));
        $this->view->epomeniSynedriasiAitiseis = $this->getEpomeniSynedriasiAitiseis();
        $this->view->sessionPassedAitiseis = Zend_Registry::get('entityManager')
                                ->getRepository('Aitiseis_Model_AitisiBase')
                                ->findAitiseis(array(
                                    'approved' => Aitiseis_Model_AitisiBase::PENDING,
                                    'sessionpassed' => true));
    }
    
    public function unifieddocAction() {
        $nextSession = $this->view->getNextSession();
        $aitiseis = $this->getEpomeniSynedriasiAitiseis();
        if(!isset($nextSession) || count($aitiseis) <= 0) {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Δεν υπάρχουν εκκρεμότητες επόμενης συνεδρίασης'));
            $this->_helper->redirector('index');
        } else {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            $finalpdf = new Zend_Pdf();
            $extractor = new Zend_Pdf_Resource_Extractor();
            foreach($aitiseis as $aitisi) {
                $cdhelper = $this->getHelper('CreateDoc');
                $cdhelper->set_livedocxPreferedOutput('pdf');
                $pdfContents = $this->_helper->createDoc($this, $aitisi, $aitisi::template);
                $pdf = new Zend_Pdf($pdfContents);
                foreach($pdf->pages as $curPage) {
                    $finalpdf->pages[] = clone $curPage;
                }
            }
            $attachmentName = 'Synedriasi_'.$nextSession->get_num().'pdf';
            $attachment = $finalpdf->render();
            $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
            $this->getResponse()
                 ->setHeader('Content-Description', 'File Transfer')
                 ->setHeader('Content-Type', 'application/pdf')
                 ->setHeader('Content-Disposition', 'attachment; filename='.$attachmentName)
                 ->setHeader('Content-Transfer-Encoding', 'binary')
                 ->setHeader('Expires', '0')
                 ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                 ->setHeader('Pragma', 'public')
                 ->setHeader('Content-Length', $this->_helper->getBinaryDataSize($attachment));
            echo $attachment;
        }
    }
    
    protected function getEpomeniSynedriasiAitiseis() {
        $nextSession = $this->view->getNextSession();
        if($nextSession != null) {
            $epomeniSynedriasiAitiseis = Zend_Registry::get('entityManager')
                                    ->getRepository('Aitiseis_Model_AitisiBase')
                                    ->findAitiseis(array(
                                        'approved' => Aitiseis_Model_AitisiBase::PENDING,
                                        'sessionid' => $nextSession->get_id()));
        } else {
            $epomeniSynedriasiAitiseis = array();
        }
        return $epomeniSynedriasiAitiseis;
    }
}

?>