<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetCompletionText extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getCompletionText($project) {
        if($project->isComplete()) {
            return 'Ολοκληρωμένο';
        } else {
            if($project->hasOverdueDeliverables()) {
                if($project instanceof Erga_Model_SubItems_Deliverable) {
                    return 'Εκπρόθεσμο';
                } else {
                    return 'Εκπρόθεσμα παραδοτέα';
                }
            } else {
                return 'Σε εξέλιξη';
            }
        }
    }
}
?>