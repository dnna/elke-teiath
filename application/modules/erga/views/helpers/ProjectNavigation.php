<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_View_Helper_ProjectNavigation extends Zend_View_Helper_Abstract
{
    public $view;
    /**
     * @var Erga_Model_Project
     */
    protected $_project;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function projectNavigation($options = array(), $project = null) {
        $this->view->headScript()->appendFile($this->view->baseUrl('media/js/ajaxformdialog.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl("media/js/erga/navigationedit.js"));
        if(isset($project)) {
            $this->_project = $project;
        }
        $this->view->headScript()->appendScript('var projectid = '.$this->_project->get_projectid().';');
        $projectnavigation = Zend_Registry::get('projectnavigation');
        $project = $this->view->getProject();
        $str = '<ul class="projectnavigation2">';
        foreach($projectnavigation as $curPage) {
            //$curPage = new Zend_Navigation_Page_Mvc(); // DEBUG
            if(isset($project) && (!isset($curPage->complexprojects) || (
                    ($curPage->complexprojects == 0 && $project->get_iscomplex() == 0) ||
                    ($curPage->complexprojects == 1 && $project->get_iscomplex() == 1)
            ))) {
                $params = $curPage->getParams();
                $params['projectid'] = $project->get_projectid();
                $curPage->setParams($params);
                if(!$curPage->isActive(true)) {
                        if($curPage->getClass() != "") {
                            $class = $curPage->getClass();
                        } else {
                            $class = 'item';
                        }
                        $str .= $this->createMenuLink($curPage, $class);
                } else if($curPage->isActive(true)) {
                    $str .= $this->createMenuLink($curPage, 'active');
                }
            } else if(!isset($project) && (!isset($curPage->complexprojects) || $curPage->complexprojects == 1)) {
                $str .= $this->createMenuLink($curPage, 'lockeditem');
            }
        }
        $str .= '</ul>';
        $str .= '<div class="epilogionomatosypoergwn" style="display: none;"></div>';
        return $str;
    }

    protected function createMenuLink(Zend_Navigation_Page $curPage, $class) {
        if($curPage->get('id') != null && $curPage->get('id') === 'ypoerga') {
            $subproejctsname = $this->_project->get_subprojectsname();
            $curPage->setLabel($subproejctsname['namepl']);
        }

        if($class !== 'active' && $class !== 'lockeditem') {
            $content = '<a href="'.$curPage->getHref().'">'.$curPage->getLabel().'</a>';
        } else {
            $content = $curPage->getLabel();
        }

        if($curPage->get('id') != null && $curPage->get('id') === 'ypoerga' && $class !== 'lockeditem') {
            $content .= ' <img src="'.$this->view->baseUrl('images/updatestatus.png').'" id="renamesubprojects" class="renamesubprojects" alt="Μετονομασία" title="Μετονομασία" />';
        }
        return '<li class="'.$class.'">'.$content.'</li>';
    }
}
?>