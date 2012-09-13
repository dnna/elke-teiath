<?php
class Aitiseis_Action_Helper_GetMapping extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($input = null)
    {
        if($input == null) {
            return 'Aitiseis_Model_AitisiBase';
        }
        $aitiseistypes = Aitiseis_Model_AitisiBase::getAitiseisTypes();
        if(isset($aitiseistypes[$input])) {
            return $aitiseistypes[$input];
        } else {
            throw new Exception('Ο συγκεκριμένος τύπος αίτησης δεν υπάρχει.');
        }
    }
}
?>