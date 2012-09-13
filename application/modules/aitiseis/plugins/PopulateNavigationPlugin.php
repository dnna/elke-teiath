<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Plugin_PopulateNavigationPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ('aitiseis' != $request->getModuleName()) {
            // Αν δεν είμαστε στο module του API τότε επιστρέφουμε
            return;
        }

        $container = Zend_Registry::get('navigation');
        $aitiseistypes = Zend_Controller_Action_HelperBroker::getStaticHelper('GetAitiseisTypes')->direct();
        foreach($aitiseistypes as $curMapping => $curName) {
            $page = new Zend_Navigation_Page_Mvc(array('label' => $curName, 'module' => 'aitiseis', 'params' => array('type' => $curMapping)));
            $aitiseiscontainer = $container->findOneBy('id', 'aitiseis');
            $aitiseiscontainer->addPage($page);
        }
    }
}
?>