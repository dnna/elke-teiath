<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Praktika_Form_Committee_Diagonismou extends Dnna_Form_SubFormBase {
    public function init() {
        Praktika_Form_Committee::addGenCommitteeFields($this);
    }
}
?>