<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_ViewController extends Zend_Controller_Action {
    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() || (!$auth->getStorage()->read()->hasRole('professor') && !$auth->getStorage()->read()->hasRole('elke'))) {
            $this->_helper->redirector('index', 'Login', 'default');
        }

        $this->view->type = $this->_helper->getMapping($this->_request->getUserParam('type', 'ypovoliergou'));
        $type = $this->view->type;
        $this->view->pageTitle = "Εμφάνιση Αιτήσεων - ".$type::type;
    }

    public function postDispatch() {
        if(isset($this->view->type)) {
            $this->view->reversetype = $this->_helper->getReverseMapping($this->view->type);
        } else {
            $this->view->reversetype = 'ypovoliaitimatos';
        }
    }

    public function indexAction() {
        if($this->view->type != null) {
            $this->view->filters = $this->_helper->filterHelper($this, 'Aitiseis_Form_AitiseisFilters');
            $auth = Zend_Auth::getInstance();
            if(!$auth->getStorage()->read()->hasRole('elke')) {
                $this->view->filters['creator'] = $auth->getStorage()->read()->get_userid();
            }
            $this->view->user = $auth->getStorage()->read();
            $qb = Zend_Registry::get('entityManager')->getRepository($this->view->type)->findAitiseisQb($this->view->filters);
            $this->view->entries = new Zend_Paginator(new Application_Plugin_QbPaginatorAdapter($qb));
            $this->view->entries->setCurrentPageNumber($this->_helper->getPageNumber($this));
        } else {
            throw new Exception('Ο συγκεκριμένος τύπος αίτησης δεν υπάρχει.');
        }
    }

    public function exportAction() {
        if($this->getRequest()->getParam('aitisiid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί aitisiid.');
        }
        // Έλεγχος ότι η αίτηση υπάρχει
        $em = Zend_Registry::get('entityManager');
        $aitisi = $em->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        if($aitisi == null) {
            throw new Exception("Η συγκεκριμένη αίτηση δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($aitisi);
        }
        $em->detach($aitisi);

        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $attachmentName = $this->_helper->getAitisiAttachmentName($aitisi->get__classname());
        // Headers
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(TRUE);
        $attachment = $this->_helper->createDoc($this, $aitisi, $aitisi::template);
        $this->getResponse()
             ->setHeader('Content-Description', 'File Transfer')
             ->setHeader('Content-Type', $options['livedocx']['mimeType'])
             ->setHeader('Content-Disposition', 'attachment; filename='.$attachmentName)
             ->setHeader('Content-Transfer-Encoding', 'binary')
             ->setHeader('Expires', '0')
             ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
             ->setHeader('Pragma', 'public')
             ->setHeader('Content-Length', $this->_helper->getBinaryDataSize($attachment));
        echo $attachment;
    }

    public function getattachmentAction() {
        if($this->getRequest()->getParam('aitisiid') == null) { // Αποφυγή bug σε περίπτωση που δεν έχει οριστεί η παράμετρος
            throw new Exception('Δεν έχει οριστεί aitisiid.');
        }
        // Έλεγχος ότι η αίτηση υπάρχει
        $em = Zend_Registry::get('entityManager');
        $aitisi = $em->getRepository('Aitiseis_Model_AitisiBase')->find($this->getRequest()->getParam('aitisiid', null));
        if($aitisi == null) {
            throw new Exception("Η συγκεκριμένη αίτηση δεν υπάρχει.");
        }
        if(!isset($this->view->type)) {
            $this->view->type = get_class($aitisi);
        }
        $attachmentName = $aitisi->get_attachmentname();
        $attachment = $aitisi->get_attachment();

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $resp = $this->getResponse();
        $resp->setHeader('Content-Description', 'File Transfer');
        $resp->setHeader('Content-Type', 'application/octet-stream');
        $resp->setHeader('Content-Disposition', 'attachment; filename='.$attachmentName);
        $resp->setHeader('Content-Transfer-Encoding', 'binary');
        $resp->setHeader('Expires', '0');
        $resp->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $resp->setHeader('Pragma', 'public');
        $resp->setHeader('Content-Length', $this->_helper->getBinaryDataSize($attachment));
        echo $attachment;
    }

    public function feedAction() {
        $type = $this->_helper->getMapping($this->_request->getParam('type', 'ypovoliergou'));
        if($type != null) {
            // Βρίσκουμε τα έργα του tokenuser (sorted σε φθήνουσα σειρά με βάση το lastupdatedate)
            $filters = $this->_helper->filterHelper($this, 'Aitiseis_Form_AitiseisFilters');
            if(!$this->view->tokenuser->hasRole('elke')) {
                $filters['creator'] = $this->view->tokenuser->get_userid();
            }
            $qb = Zend_Registry::get('entityManager')->getRepository($type)->findAitiseisQb($filters);
            $qb->orderBy('a._lastupdatedate', 'DESC');
            $aitiseis = $qb->getQuery()->getResult();
            // Δημιουργία των rss entries
            $feed = array();
            $feed['entries'] = array();
            foreach($aitiseis as $curAitisi) {
                /* @var $curAitisi Aitiseis_Model_AitisiBase */
                $entry = array(); //Container for the entry before we add it on
                $entry['title'] = $curAitisi->__toString(); //The title that will be displayed for the entry
                $entry['link'] = htmlentities($this->_request->getScheme().'://'.$this->_request->getHttpHost().$this->view->url(array('module' => 'aitiseis', 'controller' => 'view', 'action' => 'export', 'aitisiid' => $curAitisi->get_aitisiid()), null, true)); //The url of the entry
                $entry['author'] = $curAitisi->get_creator()->get_realnameLowercase();
                $entry['description'] = $curAitisi->__toString(); //Short description of the entry
                //$entry['content'] = $curProject->get_basicdetails()->get_description(); //Long description of the entry
                //Some optional entries, usually the more info you can provide, the better
                $entry['lastUpdate'] = $curAitisi->get_lastupdatedate()->getTimestamp(); //Unix timestamp of the last modified date
                //$entry['comments'] = $object->commentsUrl; //Url to the comments page of the entry
                //$entry['commentsRss'] = $object->commentsRssUrl; //Url of the comments pages rss feed
                $feed['entries'][] = $entry;
            }
            $this->_helper->createRss($this, $feed);
        } else {
            throw new Exception('Ο συγκεκριμένος τύπος αίτησης δεν υπάρχει.');
        }
    }
    
    public function ajaxformAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_request->getParams();
        if(isset($params['type'])) {
            $aitisiclassname = $this->_helper->getMapping($params['type']);
        } else {
            throw new Exception('Δεν έχει οριστεί η παράμετρος type.');
        }
        if(isset($params['aitisiid']) && $params['aitisiid'] != null) {
            $aitisi = Zend_Registry::get('entityManager')->getRepository('Aitiseis_Model_AitisiBase')->find($params['aitisiid']);
            $params = $params + $aitisi->getOptions();
        }
        if(!isset($aitisi)) {
            $aitisi = new $aitisiclassname();
        }
        $formclass = $aitisi::formclass;
        $genform = new $formclass($aitisi, $this->view);
        if(isset($params['subform']) && $params['subform'] != '') {
            $subform = $genform->getSubForm($params['subform']);
            if($subform != null && $subform instanceof Application_Form_AjaxSubFormBase) {
                $subform->setAjaxParams($params);
                $subform->ajaxInit();
                $genform->populate($aitisi);
                $this->view->subform = $subform;
            } else {
                throw new Exception('Η συγκεκριμένη υποφόρμα δεν υπάρχει ή δεν είναι του σωστού τύπου.');
            }
        } else {
            throw new Exception('Δεν επιλέξατε το όνομα της υποφόρμας.');
        }
        echo $this->view->subform;
    }
}

?>