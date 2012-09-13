<?php
class Application_Form_Subforms_DeliverableSelect extends Dnna_Form_SubFormBase {
    protected $_defaultDeliverable;
    protected $_required = true;

    public function __construct($options = array(), $view = null) {
        if(isset($options['default'])) {
            $this->_defaultDeliverable = $options['default'];
        }
        if(isset($options['required'])) {
            $this->_required = $options['required'];
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();
        // Deliverable ID
        $element = new Application_Form_Element_Flexbox('recordid');
        $element->setLabel('Επιλογή Παραδοτέου:');
        $element->setValue('null');
        $element->setRequired($this->_required);
        $this->addElement($element);
        // Όνομα Αίτησης
        $this->addElement('hidden', 'title', array(
            'value' => $this->_defaultDeliverable,
            'ignore' => true,
            )
        );
        $this->getElement('title')->setAllowEmpty(false);
        //$this->getElement('title')->addValidator(new Application_Form_Validate_Deliverable($this->_required));
    }
}
?>