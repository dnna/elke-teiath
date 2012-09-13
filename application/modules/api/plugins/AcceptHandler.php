<?php
/**
 * @author Joshua Thijssen
 * @link http://www.enrise.com/2011/01/rest-style-context-switching-part-2/
 */
class Api_Plugin_AcceptHandler extends Zend_Controller_Plugin_Abstract
{
    public function __construct($moduleName) {
        $this->__moduleName = strtolower($moduleName);
    }

    public function routeShutdown (Zend_Controller_Request_Abstract $request)
    {
        if ($this->__moduleName != $request->getModuleName()) {
            // If not in this module, return early
            return;
        }
        // Skip header check when we don't have a HTTP request (for instance: cli-scripts)
        if (! $request instanceof Zend_Controller_Request_Http) {
            return;
        }

        $this->getResponse()->setHeader('Vary', 'Accept');

        if($this->_request->getParam('format')) { // Check if the user has explicitly set the format
            $header = $this->_request->getParam('format');
        } else { // Get the Accept header
            $header = $request->getHeader('Accept');
        }
        switch (true) {
            // Depending on the value, set the correct format
            case (strstr($header, 'json')) :
                $request->setParam('format', 'json');
                break;
            case (strstr($header, 'xml')) :
                $request->setParam('format', 'xml');
                break;
            default:
                // Default: return whatever is default, but only when the format is not set
                $request->setParam('format', 'xml');
                break;
        }
    }
}
?>