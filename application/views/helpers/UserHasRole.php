<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_UserHasRole extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function userHasRole($role) {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $userObject = $auth->getStorage()->read();
            if($userObject->hasRole($role)) {
                return true;
            }
        } else if($role === 'anonymous') {
            return true;
        };

        return false;
    }
}
?>