<?php
class Aitiseis_Action_Helper_GetAitisiAttachmentName extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($type)
    {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        return $type::template.'.'.$options['livedocx']['preferedOutput'];
    }
}
?>