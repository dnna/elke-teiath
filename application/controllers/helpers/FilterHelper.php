<?php
class Application_Action_Helper_FilterHelper extends Zend_Controller_Action_Helper_Abstract
{
    function direct(Zend_Controller_Action $controller, $filterForm) {
        $filtersfromparams = $controller->getRequest()->getParam('filters');

        $filterform = new $filterForm($controller->view);
        if(is_array($filtersfromparams)) {
            $filterform->getSubForm('filters')->isValid($filtersfromparams);
        }
        $filterform->setAttrib('id', 'filters');
        $filters = $filterform->getValues();
        $filters = @$filters['filters'];
        $controller->view->filterform = $filterform;
        $controller->view->filters = $filters;
        return $filters;
    }
}
?>