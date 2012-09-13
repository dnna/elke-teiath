<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetApprovalIcon extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getApprovalIcon(Aitiseis_Model_AitisiBase $aitisi) {
        if($aitisi->get_approved() == Aitiseis_Model_AitisiBase::PENDING) {
            $now = new EDateTime('now');
            if($aitisi->get_session() != null) {
                return '<img title="Συνεδρίαση '.$aitisi->get_session().'" class="aitisipending" src="'.$this->view->baseUrl('images/pending.gif').'" style="display:inline">';
            } else {
                return '<img title="Εκκρεμεί" class="aitisipending" src="'.$this->view->baseUrl('images/pending.gif').'" style="display:inline">';
            }
        } else if($aitisi->get_approved() == Aitiseis_Model_AitisiBase::APPROVED) {
            return '<img title="Εγκρίθηκε '.$aitisi->get_session().'" class="aitisiapproved" src="'.$this->view->baseUrl('images/tick.png').' " style="display:inline">';
        } else {
            return '<img title="Απορρίφθηκε '.$aitisi->get_session().'" class="aitisirejected" src="'.$this->view->baseUrl('images/redx.png').' " style="display:inline">';
        }
    }
}
?>