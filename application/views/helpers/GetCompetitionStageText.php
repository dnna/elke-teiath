<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetCompetitionStageText extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getCompetitionStageText(Praktika_Model_Competition $competition) {
        $stage = $competition->get_competitionstage();
        if($stage === '1.0') {
            return 'Δεν έχει γίνει ανάθεση';
        } else if($stage === '1.1') {
            return 'Ανάθεση '.$competition->get_assignmentdate();
        } else if($stage === '2.0') {
            return 'Δεν έχει προκηρυχθεί';
        } else if($stage === '2.1') {
            return 'Προκηρύχθηκε '.$competition->get_noticedate();
        } else if($stage === '2.2') {
            return 'Διενεργήθηκε '.$competition->get_execdate();
        } else if($stage === '2.3') {
            return 'Κατακυρώθηκε '.$competition->get_awarddate();
        }
    }
}
?>