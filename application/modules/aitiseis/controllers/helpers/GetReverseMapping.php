<?php
class Aitiseis_Action_Helper_GetReverseMapping extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($classname)
    {
        $aitiseistypes = Aitiseis_Model_AitisiBase::getAitiseisTypes();
        foreach($aitiseistypes as $curMapping => $curClass) {
            if($curClass === $classname) {
                return $curMapping;
            }
        }
        throw new Exception('Η συγκεκριμένη κλάση δεν υπάρχει ή δεν είναι αίτηση.');
    }
}
?>