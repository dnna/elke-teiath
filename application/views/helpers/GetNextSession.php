<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetNextSession extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getNextSession() {
        $now = new EDateTime('now');
        $timestamp = $now->getTimestamp();
        $synedriasi = Zend_Registry::get('entityManager')->getRepository('Synedriaseisee_Model_Synedriasi')->findSynedriaseis(array('start' => $timestamp), 1);
        if(count($synedriasi) > 0) {
            return $synedriasi[0];
        } else {
            return null;
        }
    }
}
?>