<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_IndexController extends Zend_Controller_Action {
    public function indexAction() {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $auth->getStorage()->read()->hasRole('professor')) {
            $this->_helper->redirector('index', 'Professor_View');
        } else if($auth->hasIdentity() && $auth->getStorage()->read()->hasRole('elke')) {
            $this->_helper->redirector('index', 'Diaxeirisi');
        }
    }
}
?>