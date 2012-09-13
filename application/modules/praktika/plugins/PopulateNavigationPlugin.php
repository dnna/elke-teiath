<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_Plugin_PopulateNavigationPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ('praktika' != $request->getModuleName()) {
            // Αν δεν είμαστε στο module του API τότε επιστρέφουμε
            return;
        }

        /* @var $container Zend_Navigation */
        $container = Zend_Registry::get('navigation');

        $epitropes = $container->findOneBy('id', 'epitropes');
        $praktika = $container->findOneBy('id', 'praktika');
        /*if(isset($epitropes) && $epitropes->isActive(true)) {
            $newcontainer = $epitropes;
            $controller = 'epitropes';
            $types = Praktika_Model_CommitteeBase::getEpitropesTypesText();
        } else */if(isset($praktika) && $praktika->isActive(true)) {
            $newcontainer = $praktika;
            $controller = 'praktika';
            $types = Praktika_Model_PraktikoBase::getPraktikaTypesText();
        }
        if(isset($newcontainer)) {
            foreach($types as $curMapping => $curName) {
                $page = new Zend_Navigation_Page_Mvc(array('label' => $curName, 'module' => 'praktika', 'controller' => $controller, 'params' => array('type' => $curMapping)));
                $newcontainer = 
                $newcontainer->addPage($page);
            }
        }
    }
}
?>