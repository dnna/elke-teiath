<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class  Praktika_Form_Committee extends Dnna_Form_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/collapsiblefields.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/toggledetails.js', 'text/javascript'));
        
        // Id επιτροπής
        $this->addElement('hidden', 'id', array(
            'disabled' => true,
            'readonly' => true
        ));

        // Επιλογή έργου
        //$this->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => true), $this->_view), 'project');
        $this->_view->flexboxDependencies();

        // Επιλογή τύπο επιτροπής
        $subform = new Dnna_Form_SubFormBase($this->_view);
        $subform->setLegend('Είδος Επιτροπής');
        $epitropestypes = Praktika_Model_CommitteeBase::getEpitropesTypesText();
        ksort($epitropestypes);
        $subform->addElement('select', 'type', array(
            'label' => 'Είδος Επιτροπής',
            'multiOptions' => $epitropestypes,
        ));
        $this->addSubForm($subform, 'competitiontype');

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $this->addSubForm($subform, 'default');

        $subform = new Dnna_Form_SubFormBase($this->_view);
        $subform->setLegend('Μέλη Επιτροπής');
        //$this->addCommitteeFields($this, 'committeemembers', false);
        Praktika_Form_Committee_Member::addCommitteeMembers($subform, null, false);
        $this->addSubForm($subform, 'committeemembers');

        $this->addElement('hidden', 'active', array(
            /*'label' => 'Ενεργοποιημένη',
            'multiOptions' => array(0 => 'Όχι', 1 => 'Ναί'),
            'required' => true,*/
            'value' => 1
        ));

        $this->addSubmitFields();
    }

    public static function addGenCommitteeFields(Zend_Form &$subform) {
        $textareainfo = Dnna_Form_FormBase::getTextAreaInfo();
        // Τίτλος έργου
        $subform->addElement('textarea', 'comments', array(
            'label' => 'Πρόσθετες Πληροφορίες:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $textareainfo['textareaMaxLength']))
            ),
            'rows' => 1,
            'cols' => $textareainfo['textareaCols'],
            'required' => true,
            )
        );
    }
}
?>