<?php
Zend_Controller_Action_HelperBroker::getStaticHelper('EmailBase');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Action_Helper_EmailAitisi extends Dnna_Action_Helper_EmailBase {
    public function direct($aitisi, $type = 'new') {
        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom($this->_emailfromaddress, $this->_emailfromname);
        // Στέλνουμε στον χρήστη
        if($type === 'new') {
            $this->newAitisi($mail, $aitisi);
        } else if($type === 'changeapproval') {
            $this->changeApproval($mail, $aitisi);
        } else if($type === 'changesession') {
            $this->changeSession($mail, $aitisi);
        }
        
        // Μερικά extra headers για να μην πηγαίνει στο spam
        $mail->setReplyTo($this->_emailfromaddress, $this->_emailfromname);
        // Αποστολή
        $mail->send($this->_tr);
    }
    
    protected function newAitisi(Zend_Mail &$mail, $aitisi) {
        $mail->addTo($this->_emailtoaddress, $this->_emailtoname);
        $mail->setSubject($aitisi::type.' από "'.$aitisi->get_creator()->get_realname().'"');
        $mail->setBodyText('Η αίτηση έχει τίτλο "'.$aitisi->__toString().'" και είναι συνημμένη σε αυτό το email.');
        $this->attachAitisi($mail, $aitisi);
        /*// Στέλνουμε στον διαχειριστή
        $mail->addBcc($this->_emailtoaddress, $this->_emailtoname);*/
    }
    
    protected function changeApproval(Zend_Mail &$mail, $aitisi) {
        $mail->addTo($aitisi->get_creator()->get_email(), $aitisi->get_creator()->get_realname());
        $mail->setSubject('Αίτηση '.$aitisi->get_approvedtext().': "'.$aitisi->__toString().'"');
        $mail->setBodyText('Η αίτηση που υποβάλατε "'.$aitisi->get_approvedtext().'"');
        $this->attachAitisi($mail, $aitisi);
    }
    
    protected function changeSession(Zend_Mail &$mail, $aitisi) {
        $mail->addTo($aitisi->get_creator()->get_email(), $aitisi->get_creator()->get_realname());
        $mail->setSubject('Ορισμός συνεδρίασης για την αίτηση "'.$aitisi->__toString().'"');
        /* @var $aitisi Aitiseis_Model_AitisiBase */
        // Δημιουργία του link συνεδρίασης
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $opts = array('module' => 'synedriaseisee', 'controller' => 'index', 'action' => 'eventview', 'id' => $aitisi->get_session()->get_id());
        // Τα συνθέτουμε όλα μαζι σε ένα ωραίο μήνυμα
        $mail->setBodyText('Η αίτηση που υποβάλατε στις '.$aitisi->get_creationdate().' προστέθηκε στην '.$aitisi->get_session().' με αριθμό θέματος '.$aitisi->get_sessionsubject()->get_num().'.'.PHP_EOL.'Μπορείτε να βρείτε αναλυτικότερες πληροφορίες για τη συνεδρίαση στον σύνδεσμο '.$viewRenderer->view->serverUrl().$viewRenderer->view->url($opts, 'default'));
    }

    protected function attachAitisi(Zend_Mail &$mail, $aitisi) {
        $aitisiClass = get_class($aitisi);
        $createdoc = Zend_Controller_Action_HelperBroker::getStaticHelper('CreateDoc');
        $getattachmentname = Zend_Controller_Action_HelperBroker::getStaticHelper('GetAitisiAttachmentName');
        $attachment = $mail->createAttachment($createdoc->direct($this->getActionController(), $aitisi, $aitisi::template),
                                $this->_livedocxMimeType,
                                Zend_Mime::DISPOSITION_ATTACHMENT,
                                Zend_Mime::ENCODING_BASE64);
        $attachment->filename = $getattachmentname->direct($aitisiClass);
        return $attachment;
    }
}
?>