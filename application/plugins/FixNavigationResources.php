<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Plugin_FixNavigationResources
{
    // Ορίζει στις σελίδες που δεν έχουν resource το resource του γονιού τους
    public static function fixResourceNames(Zend_Navigation_Container $container) {
        foreach($container as $curPage) {
            if($curPage->hasChildren()) {
                Application_Plugin_FixNavigationResources::fixResourceNames($curPage);
            }
            if($curPage->getResource() == '' && $container->getResource() != '') {
                $curPage->setResource($container->getResource());
            }
        }
    }
}
?>