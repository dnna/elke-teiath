<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Contractor extends Application_Form_Lists_SubFormEdit {
    public function __construct($view = null) {
        parent::__construct('Application_Model_Lists_Agency', $view);
    }
}
?>