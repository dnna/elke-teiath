<?php
class Application_Form_Subforms_SynedriasiSelect extends Dnna_Form_SubFormBase {
    protected $_defaultSynedriasi;
    protected $_required = true;

    public function __construct($options = array(), $view = null) {
        if(isset($options['default'])) {
            $this->_defaultSynedriasi = $options['default'];
        }
        if(isset($options['required'])) {
            $this->_required = $options['required'];
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();

        // Συνεδρίαση id
        $element = new Application_Form_Element_Flexbox('id');
        $element->setLabel('Επιλογή Συνεδρίασης:');
        $element->setValue('null');
        $element->setRequired($this->_required);
        $this->addElement($element);
        // Όνομα Συνεδρίασης
        $this->addElement('hidden', 'title', array(
            'value' => $this->_defaultSynedriasi,
            )
        );
        $this->getElement('title')->setAllowEmpty(false);
        $this->getElement('title')->addValidator(new Application_Form_Validate_Synedriasi($this->_required));
    }
}
?>