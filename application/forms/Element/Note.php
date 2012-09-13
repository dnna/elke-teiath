<?php
class Application_Form_Element_Note extends Zend_Form_Element_Xhtml  
{  
    public $helper = 'formNote';
    
    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);
        $this->removeDecorator('Label');
        $this->setIgnore(true);
    }
}
?>