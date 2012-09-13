<?php
class Erga_Form_Subforms_Partner extends Dnna_Form_SubFormBase {
    protected $_i;
    protected $_alldetails;

    public function __construct($i, $view = null, $alldetails = true) {
        $this->_i = $i;
        $this->_alldetails = $alldetails;
        parent::__construct($view);
    }

    public function init() {
        // Recordid
        $this->addElement('hidden', 'recordid', array());
        $subform = new Dnna_Form_SubFormBase();
        $subform->addElement('select', 'id', array(
            'label' => 'Συνεργαζόμενος Φορέας:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Agency'),
        ));
        $this->addSubForm($subform, 'partnerlistitem', false);
        if($this->_alldetails == true) {
            $this->addElement('text', 'amount', array(
                'label' => 'Ποσό:',
                //'required' => true, //Αυτο πρεπει να ειναι required?
                'validators' => array(
                    array('validator' => 'Float')
                ),
                'class' => 'formatFloat',
            ));
            $this->addElement('checkbox', 'iscoordinator', array(
                'label' => 'Είναι συντονιστής;',
                'class' => 'unique',
            ));
            $subform = new Erga_Form_Subforms_Coordinator();
            $this->addSubForm($subform, 'default');
            $this->getSubForm('default')->getDecorator('Fieldset')->setOption('id', 'fieldset-position-partners-'.$this->_i.'-coordinatorfields');
            $this->getSubForm('default')->removeDecorator('DtDdWrapper');
        }
    }
}
?>