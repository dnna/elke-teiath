<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class PollingController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
    }

    public function indexAction() {}

    public function loginstatuspollAction() {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            echo "true";
        } else {
            echo "false";
        }
    }

    public function sessiontimeoutpollAction() {
        echo ini_get('session.gc_maxlifetime')*1000;
    }
}
?>