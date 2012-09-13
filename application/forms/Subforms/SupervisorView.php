<?php
class Application_Form_Subforms_SupervisorView extends Dnna_Form_SubFormBase {
    protected $_defaultSupervisor;
    
    public function __construct($defaultSupervisor = null, $view = null) {
        $this->_defaultSupervisor = $defaultSupervisor;
        parent::__construct($view);
    }
    
    public function init() {
        $defaultSupervisor = $this->_defaultSupervisor;

        if($defaultSupervisor == null || !($defaultSupervisor instanceof Application_Model_User)) {
            $userObject = Zend_Auth::getInstance()->getStorage()->read();
        } else {
            $userObject = $defaultSupervisor;
        }
        
        // User ID
        $this->addElement('hidden', 'userid', array(
            'value' => $userObject->get_userid(),
            'readonly' => true,
        ));
        // Ονοματεπώνυμο
        $this->addElement('text', 'realname', array(
            'label' => 'Ονοματεπώνυμο:',
            'value' => $userObject->get_realname(),
            'readonly' => true,
            'ignore' => true,
            )
        );
        $this->getElement('realname')->setAllowEmpty(false);
        $this->getElement('realname')->addValidator(new Application_Form_Validate_Supervisor());
        // Ιδιότητα
        $this->addElement('text', 'capacity', array(
            'label' => 'Ιδιότητα:',
            'value' => $userObject->get_capacity(),
            'readonly' => true,
            'ignore' => true,
            )
        );
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