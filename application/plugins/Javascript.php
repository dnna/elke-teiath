<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Plugin_Javascript extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        if($request->getModuleName() !== 'aitiseis') {
            $file = realpath(APPLICATION_PATH.'/../public/media/js/'.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName().'.js');
            $url = $view->baseUrl().'/media/js/'.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName().'.js';
        } else if($request->getControllerName() === 'new' || ($request->getControllerName() === 'review') && $request->getActionName() !== 'changeapproval') {
            $file = realpath(APPLICATION_PATH.'/../public/media/js/'.$request->getModuleName().'/'.$request->getParam('type').'.js');
            $url = $view->baseUrl().'/media/js/'.$request->getModuleName().'/'.$request->getParam('type').'.js';
        }
        if(isset($file) && file_exists($file)) {
            $view->headScript()->appendFile($url);
        }
    }
}
?>