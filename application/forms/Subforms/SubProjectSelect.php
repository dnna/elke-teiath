<?php
class Application_Form_Subforms_SubProjectSelect extends Dnna_Form_SubFormBase {
    protected $_defaultProject;
    protected $_required = true;

    public function __construct($options = array(), $view = null) {
        if(isset($options['default'])) {
            $this->_defaultProject = $options['default'];
        }
        if(isset($options['required'])) {
            $this->_required = $options['required'];
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();

        /*if($this->_required == false) {
            $note = new Application_Form_Element_Note('clearbutton');
            $note->setValue('<img src="'.$this->_view->baseUrl("images/delete_x.gif").'" alt="Αφαίρεση" id="clearProject" class="removeSubItem removeSubItemFieldset" title="Καθαρισμός Αίτησης">');
            $this->addElement($note);
        }*/

        // SubProject ID
        $element = new Application_Form_Element_Flexbox('subprojectid');
        $element->setLabel('Επιλογή Έργου/Υποέργου:');
        $element->setValue('null');
        $element->setRequired($this->_required);
        $this->addElement($element);
        // Όνομα Αίτησης
        $this->addElement('hidden', 'subprojecttitle', array(
            'value' => $this->_defaultProject,
            'ignore' => true,
            )
        );
        $this->getElement('subprojecttitle')->setAllowEmpty(false);
        $this->getElement('subprojecttitle')->addValidator(new Application_Form_Validate_SubProject($this->_required));
    }
}
?>