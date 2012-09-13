<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Plugin_RefererPlugin extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        // Keep the URL of the referer, unless we are in the login page
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $controllerName = strtolower($request->getControllerName());
        $actionName = strtolower($request->getActionName());
        $moduleName = strtolower($request->getModuleName());
        $refererSession = new Zend_Session_Namespace('referer');
        if($controllerName !== "login" && $controllerName !== "error" && $controllerName !== "polling" && $controllerName !== "report" &&
            $controllerName !== "export_doc" && $controllerName !== "media" && $controllerName !== "images" &&
            strpos($actionName, 'delete') === false && strpos($actionName, 'event') === false && strpos($actionName, 'feed') === false &&
            strtolower($moduleName) !== "api") {
            $refererSession->referer = $request->getUserParams();
            $refererSession->postVars = array();
            if($request->isPost()) {
                $refererSession->postVars = $request->getPost();
            }
        } else if(!isset($refererSession->referer)) {
            $refererSession->referer = array('module' => 'default', 'controller' => 'index', 'action' => 'index');
            $refererSession->postVars = array();
        }
    }
}
?>