<?php

/**
 * Παίρνει ένα doc αρχείο και αντικαθιστά κάποια strings μέσα σε αυτό. Στη
 * συγκεκριμένη εφαρμογή χρησιμοποιείται για την παραγωγή των αιτήσεων μέσα από
 * τις φόρμες.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Action_Helper_CreateRss extends Zend_Controller_Action_Helper_Abstract {

    /**
     * @return Zend_Feed
     */
    public function direct(Zend_Controller_Action $controller, $feed = array()) {
        $request = $controller->getRequest();
        //Setup some info about our feed
        if(!isset($feed['title'])) {
            if(isset($controller->view->pageTitle)) {
                $feed['title'] = $controller->view->pageTitle;
            } else {
                $feed['title'] = 'RSS Feed';
            }
        }
        if(!isset($feed['link'])) {
            $feed['link'] = htmlentities($request->getScheme().'://'.$request->getHttpHost().$controller->view->url());
        }
        if(!isset($feed['charset'])) {
            $feed['charset'] = 'utf-8';
        }
        if(!isset($feed['language'])) {
            $feed['language'] = 'el-gr';
        }
        if(!isset($feed['published'])) {
            $feed['published'] = time();
        }

        $feedObj = Zend_Feed::importArray($feed, $request->getParam('feedType'));
        //Return the feed as a string, we're not ready to output yet
        //$feedString = $feedObj->saveXML();
        //Or we can output the whole thing, headers and all, with
        $controller->getHelper('layout')->disableLayout();
        $controller->getHelper('viewRenderer')->setNoRender(TRUE);
        $feedObj->send();
    }
}

?>