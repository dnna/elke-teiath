<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetUserInfo extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getUserInfo() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userObject = $auth->getStorage()->read();
            return $userObject;
        }
        return false;
    }
}
?>