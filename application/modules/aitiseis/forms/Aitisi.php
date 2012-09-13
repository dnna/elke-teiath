<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Aitiseis_Form_Aitisi extends Dnna_Form_FormBase {
    /**
     * @var Aitiseis_Model_AitisiBase
     */
    protected $_aitisi;

    public function __construct(Aitiseis_Model_AitisiBase $aitisi, $view = null) {
        $this->_aitisi = $aitisi;
        parent::__construct($view);
    }
}

?>