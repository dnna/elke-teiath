<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_View_Helper_GetOrderLink extends Zend_View_Helper_Abstract
{
    public $view;
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function getOrderLink($sort, $name) {
        $cursort = Zend_Controller_Front::getInstance()->getRequest()->getParam('sort', '-');
        $curorder = Zend_Controller_Front::getInstance()->getRequest()->getParam('order', 'ASC');
        $filters = Zend_Controller_Front::getInstance()->getRequest()->getParam('filters');
        if($curorder === 'ASC' && $cursort === $sort) {
            $curIcon = $this->view->baseUrl('images/des.gif');
            $order = 'DESC';
        } else if($curorder === 'DESC' && $cursort === $sort) {
            $curIcon = $this->view->baseUrl('images/asc.gif');
            $order = 'ASC';
        } else {
            $curIcon = $this->view->baseUrl('images/small.gif');
            $order = 'DESC';
        }
        return '<div class="orderinnerwrapper">
                    <a class="orderlink" href="'.$this->view->url(array('sort' => $sort, 'order' => $order)).'"><img src="'.$curIcon.'" alt="Order" class="orderimg" />'.$name.'</a>
                </div>';
    }
}
?>