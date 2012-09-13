<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_Lists_SubFormEdit extends Dnna_Form_AutoForm {
    /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;
    
    public function __construct($type, $view = null) {
        parent::__construct($type, $view);
        $tempsubform = new Zend_Form_SubForm();
        $this->setDecorators($tempsubform->getDecorators());
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());

        $this->createFieldsFromType();
    }
}
?>