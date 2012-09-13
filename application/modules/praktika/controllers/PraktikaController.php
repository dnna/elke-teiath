<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Praktika_PraktikaController extends Zend_Controller_Action {
    public function indexAction() {
        $this->_request->setParam('format', null);
    }
}
?>