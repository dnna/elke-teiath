<?php
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
abstract class Application_Form_AjaxSubFormBase extends Dnna_Form_SubFormBase {
    public function stripExternalDecorators() {
        $this->removeDecorator('DtDdWrapper');
        $this->removeDecorator('Fieldset');
    }

    public function __construct($params = null, $view = null) {
        parent::__construct($view);
    }

    public abstract function ajaxInit();

    /**
     * Παράμετροι που χρησιμοποιούνται όταν η φόρμα καλείται μέσω Ajax. Η κάθε
     * φόρμα είναι υπεύθυνη να διαχειρίζεται τις παραμέτρους που την αφορούν.
     */
    public abstract function setAjaxParams($params);
}
?>