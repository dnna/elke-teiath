<?php
class Application_Action_Helper_GetPageNumber extends Zend_Controller_Action_Helper_Abstract
{
    function direct(Zend_Controller_Action $controller) {
        return $controller->getRequest()->getParam('page', 1);
    }
}
?>