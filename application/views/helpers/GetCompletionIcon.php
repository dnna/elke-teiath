<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetCompletionIcon extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getCompletionIcon($project) {
        if($project->isComplete()) {
            return '<img title="'.$this->view->getCompletionText($project).'" class="deliverablecomplete" src="'.$this->view->baseUrl('images/tick.png').' " style="display:inline">';
        } else {
            if($project->hasOverdueDeliverables()) {
                return '<img title="'.$this->view->getCompletionText($project).'" class="deliverableoverdue" src="'.$this->view->baseUrl('images/overduedeliverables.gif').'" style="display:inline">';
            } else {
                return '<img title="'.$this->view->getCompletionText($project).'" class="deliverableinprogress" src="'.$this->view->baseUrl('images/pending.gif').'" style="display:inline">';
            }
        }
    }
}
?>