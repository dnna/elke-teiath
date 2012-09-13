<?php

/**
 * Παίρνει ένα doc αρχείο και αντικαθιστά κάποια strings μέσα σε αυτό. Στη
 * συγκεκριμένη εφαρμογή χρησιμοποιείται για την παραγωγή των αιτήσεων μέσα από
 * τις φόρμες.
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Action_Helper_CreateRedmineIssue extends Zend_Controller_Action_Helper_Abstract {
    protected $_config;

    public function init() {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->_config = $bootstrap->getOptions();
        return parent::init();
    }

    /**
     * @return Zend_Feed
     */
    public function direct(Zend_Controller_Action $controller, $data) {
        if(isset($data['description'])) {
            $data['description'] = 'Περιγραφή: '.$data['description'];
        } else {
            $data['description'] = '';
        }
        if(isset($data['subject'])) {
            // Προσαρτούμε επιπλέον πληροφορίες στο description αν είναι διαθέσιμες
            $refererSession = new Zend_Session_Namespace('referer');
            if(isset($refererSession->referer)) {
                if(!empty($data['description'])) {
                    $data['description'] .= "\n\n";
                }
                $data['description'] .= 'Referer: '.$controller->view->url($refererSession->referer, null, true);
            }
            if(count($refererSession->postVars) > 0) {
                if(!empty($data['description'])) {
                    $data['description'] .= "\n\n";
                }
                $data['description'] .= 'POST Vars: '.http_build_query($refererSession->postVars);
            }

            $reqXml = "<?xml version=\"1.0\"?>";
            $reqXml .= "<issue>";
            $reqXml .= "<subject>".$data['subject']."</subject>";
            $reqXml .= "<description>".$data['description']."</description>";
            $reqXml .= "<project_id>".$this->_config['report']['redmineProjectId']."</project_id>";
            $reqXml .= "</issue>";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_config['report']['redmineUrl']);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_USERPWD, $this->_config['report']['redmineKey'].":password");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $reqXml);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_FAILONERROR,1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        } else{
            return false;
        }
    }
}

?>