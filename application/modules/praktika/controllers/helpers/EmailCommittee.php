<?php
Zend_Controller_Action_HelperBroker::getStaticHelper('EmailBase');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_Action_Helper_EmailCommittee extends Dnna_Action_Helper_EmailBase {
    public function direct($committee, $type = 'new') {
        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom($this->_emailfromaddress, $this->_emailfromname);
        // Στέλνουμε στον χρήστη
        if($type === 'informepitropiparalavis') {
            $this->informEpitropiParalavis($mail, $committee);
        }

        // Μερικά extra headers για να μην πηγαίνει στο spam
        $mail->setReplyTo($this->_emailfromaddress, $this->_emailfromname);
        // Αποστολή
        $mail->send($this->_tr);
    }

    protected function informEpitropiParalavis(Zend_Mail &$mail, Praktika_Model_Committee_Paralavis $committee) {
        /* @var $curCommitteeMember Aitiseis_Model_DhmiourgiaEpitropisParalavis_CommitteeMember */
        foreach($committee->get_committeemembers() as $curCommitteeMember) {
            $mail->addTo($curCommitteeMember->get_user()->get_email(), $curCommitteeMember->get_user()->get_realnameLowercase());
        }
        if($committee->get_aitisi() != null) {
            $bodytext = 'Μετά από αίτημα του επιστημονικά υπευθύνου "'.$committee->get_aitisi()->get_creator()->get_realnameLowercase().'", προστεθήκατε';
        } else {
            $bodytext = 'Προστεθήκατε';
        }
        $mail->setSubject('Προστεθήκατε στην επιτροπή παραλαβής του έργου '.$committee->get_project()->__toString());
        $mail->setBodyText($bodytext.' στην επιτροπή παραλαβής για το έργο με κωδικό "'.$committee->get_project()->get_code().'" και τίτλο "'.$committee->get_project()->__toString().'".');
    }
}
?>