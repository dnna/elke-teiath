<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Help_CatchallController extends Zend_Controller_Action {
    public function indexAction() {
        $uri = str_replace($this->view->baseUrl().'/'.$this->_request->getModuleName().'/', '', $this->_request->getRequestUri());
        if($uri === '/'.$this->_request->getModuleName()) { // Έχει παραλειφθεί το / στο τέλος
            $this->_helper->redirector->gotoUrl($this->view->baseUrl().'/'.$this->_request->getModuleName().'/');
        }
        $this->_helper->viewRenderer->setNoController(true);
        $this->findTemplate($uri);
        $this->view->headLink()->appendStylesheet($this->view->baseUrl('media/css/help.css'));
        $this->view->headScript()->appendFile($this->view->baseUrl("media/js/help.js"));

        $this->view->breadcrumbs = $this->createNavigation($uri);
    }
    
    protected function createNavigation($uri) {
        $navigation = new Zend_Navigation();
        /* @var $toc Application_View_Helper_TableOfContents */
        $toc = $this->view->getHelper('TableOfContents');
        
        // Start page
        $startPage = new Zend_Navigation_Page_Uri(array('uri' => $this->view->baseUrl().'/'.$this->_request->getModuleName().'/'));
        $startPage->setLabel('Βοήθεια');
        $startPage->setActive(true);
        $navigation->addPage($startPage);
        
        // Current page
        $curPage = new Zend_Navigation_Page_Uri(array('uri' => $uri));
        $curPagePath = realpath($this->view->getScriptPath($this->_helper->viewRenderer->getViewScript()));
        $curPageName = $toc->getName($curPagePath);
        $curPage->setLabel($curPageName['name']);
        $curPage->setActive(true);
        $startPage->addPage($curPage);
        return $navigation;
    }

    protected function findTemplate($curTemplate) {
        $this->_helper->viewRenderer->setRender($curTemplate);
        try {
            $this->_helper->viewRenderer->render();
        } catch(Exception $e) {
            // View script doesn't exist
            $this->_helper->viewRenderer->setNoRender(false);
            $upperlevel = $this->getUpperLevelTemplate($curTemplate);
            if($upperlevel != false) {
                return $this->findTemplate($upperlevel);
            }
        }
    }

    protected function getUpperLevelTemplate($curTemplate) {
        if(basename($curTemplate) !== 'index') {
            return $curTemplate.'/index';
        } else if(dirname($curTemplate) !== '.' && dirname($curTemplate) !== DIRECTORY_SEPARATOR) {
            return dirname(dirname($curTemplate)).'/index';
        } else {
            return 'index';
        }
    }
}

?>