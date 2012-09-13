<?php
class Application_Form_Subforms_SubjectSelect extends Dnna_Form_SubFormBase {
    protected $_defaultSubject;
    protected $_required = true;

    public function __construct($options = array(), $view = null) {
        if(isset($options['default'])) {
            $this->_defaultSubject = $options['default'];
        }
        if(isset($options['required'])) {
            $this->_required = $options['required'];
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();

        // Συνεδρίαση id
        $element = new Application_Form_Element_Flexbox('recordid');
        $element->setLabel('Επιλογή Θέματος:');
        $element->setValue('null');
        $element->setRequired($this->_required);
        $this->addElement($element);
        // Όνομα Θέματος
        $this->addElement('hidden', 'titlewithnum', array(
            'value' => $this->_defaultSubject,
            )
        );
        $this->getElement('titlewithnum')->setAllowEmpty(false);
        $this->getElement('titlewithnum')->addValidator(new Application_Form_Validate_Subject($this->_required));
    }
}
?>