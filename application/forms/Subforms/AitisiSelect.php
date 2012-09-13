<?php
class Application_Form_Subforms_AitisiSelect extends Dnna_Form_SubFormBase {
    protected $_defaultAitisi;
    protected $_type = null;
    protected $_approved;
    protected $_required = true;
    protected $_selecttype = false;

    public function __construct($options = array(), $view = null) {
        if(isset($options['default'])) {
            $this->_defaultAitisi = $options['default'];
        }
        if(isset($options['type'])) {
            $this->_type = $options['type'];
        }
        if(isset($options['approved'])) {
            $this->_approved = $options['approved'];
        }
        if(isset($options['required'])) {
            $this->_required = $options['required'];
        }
        if(isset($options['selecttype'])) {
            $this->_selecttype = $options['selecttype'];
        }
        parent::__construct($view);
    }

    public function init() {
        $this->_view->flexboxDependencies();

        /*if($this->_required == false) {
            $note = new Application_Form_Element_Note('clearbutton');
            $note->setValue('<img src="'.$this->_view->baseUrl("images/delete_x.gif").'" alt="Αφαίρεση" id="clearAitisi" class="removeSubItem removeSubItemFieldset" title="Καθαρισμός Αίτησης">');
            $this->addElement($note);
        }*/

        // Τύπος Αίτησης
        if($this->_selecttype == true) {
            $front = Zend_Controller_Front::getInstance();
            $aitiseismoduledir = $front->getModuleDirectory('aitiseis');
            require_once($aitiseismoduledir.'/controllers/helpers/GetAitiseisTypes.php');
            $mappinghelper = new Aitiseis_Action_Helper_GetAitiseisTypes();
            $this->addElement('select', 'shorttype', array(
                'label' => 'Τύπος αίτησης:',
                'required' => $this->_required,
                'multiOptions' => $mappinghelper->direct(),
                'ignore' => true,
            ));
        }
        // Αίτηση ID
        $element = new Application_Form_Element_Flexbox('aitisiid');
        $element->setLabel('Επιλογή Αίτησης:');
        $element->setValue('null');
        $element->setRequired($this->_required);
        $this->addElement($element);
        // Όνομα Αίτησης
        $this->addElement('hidden', 'aitisiname', array(
            'value' => $this->_defaultAitisi,
            )
        );
        $this->getElement('aitisiname')->setAllowEmpty(false);
        $this->getElement('aitisiname')->addValidator(new Application_Form_Validate_Aitisi($this->_type, $this->_approved, $this->_required));
    }
}
?>