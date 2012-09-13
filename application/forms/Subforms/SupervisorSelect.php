<?php
class Application_Form_Subforms_SupervisorSelect extends Dnna_Form_SubFormBase {
    protected $_defaultSupervisor;
    
    public function __construct($defaultSupervisor = null, $view = null) {
        $this->_defaultSupervisor = $defaultSupervisor;
        parent::__construct($view);
    }
    
    public function init() {
        $this->_view->flexboxDependencies();

        $defaultSupervisor = $this->_defaultSupervisor;

        if($defaultSupervisor == null || !($defaultSupervisor instanceof Application_Model_User)) {
            $userObject = Zend_Auth::getInstance()->getStorage()->read();
        } else if(isset($defaultSupervisor)) {
            $userObject = $defaultSupervisor;
        } else {
            $userObject = new Application_Model_User();
            $userObject->set_userid('null');
        }

        // User ID
        $element = new Application_Form_Element_Flexbox('userid');
        $element->setLabel('Επιστημονικά Υπεύθυνος:');
        $element->setValue($userObject->get_userid());
        $this->addElement($element);
        // Ονοματεπώνυμο
        $this->addElement('hidden', 'realname', array(
            'value' => $userObject->get_realname(),
            'ignore' => true,
            )
        );
        // Ιδιότητα
        $this->addElement('text', 'capacity', array(
            'label' => 'Ιδιότητα:',
            'value' => $userObject->get_capacity(),
            'readonly' => true,
            'ignore' => true,
            )
        );
        $this->getElement('capacity')->setAllowEmpty(false);
        $this->getElement('capacity')->addValidator(new Application_Form_Validate_Supervisor(true));
        // Τμήμα
        $this->addElement('text', 'departmentname', array(
            'label' => 'Τμήμα:',
            'value' => $userObject->get_departmentname(),
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Τομέας
        $this->addElement('text', 'sector', array(
            'label' => 'Τομέας:',
            'value' => $userObject->get_sector(),
            'readonly' => true,
            'ignore' => true,
            )
        );
        // Τηλ. & Fax
        $this->addElement('text', 'phone', array(
            'label' => 'Τηλ. & Fax:',
            'value' => $userObject->get_phone(),
            'readonly' => true,
            'ignore' => true,
            )
        );
        // e-mail
        $this->addElement('text', 'email', array(
            'label' => 'e-mail:',
            'value' => $userObject->get_email(),
            'readonly' => true,
            'ignore' => true,
        ));
    }
}
?>