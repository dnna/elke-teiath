<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetProjectLink extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getProjectLink(Erga_Model_Project $project = null) {
        if($project == null) {
            return "";
        }
        return $this->view->url(array('module' => 'erga', 'controller' => 'Diaxeirisi', 'action' => 'review', 'projectid' => $project->get_projectid()), 'default', true);
    }
}
?>