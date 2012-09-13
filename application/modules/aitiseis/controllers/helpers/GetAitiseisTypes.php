<?php
class Aitiseis_Action_Helper_GetAitiseisTypes extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        $aitiseistypes = Aitiseis_Model_AitisiBase::getAitiseisTypes();
        $mappings = array();
        foreach($aitiseistypes as $curMapping => $curClass) {
            $mappings[$curMapping] = $curClass::type;
        }
        return $mappings;
    }
}
?>