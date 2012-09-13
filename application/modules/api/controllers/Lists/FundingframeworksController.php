<?php
require_once('ListsController.php');
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Api_Lists_FundingframeworksController extends Api_Lists_ListsController
{
    public function init() {
        parent::init();
        $this->view->type = 'fundingframeworks';
        $this->view->classname = 'Application_Model_Lists_FundingFramework';
    }

    public function getAction() {
        $object = Zend_Registry::get('entityManager')->getRepository($this->view->classname)->find($this->_request->getParam('id'));
        if(!isset($object)) {
            throw new Exception('Το αντικείμενο δεν βρέθηκε.', 404);
        }
        $form = $this->getFundingFrameworkForm();
        $this->_helper->Get($this, $object, $form);
    }

    public function schemaAction() {
        echo $this->_helper->generateXsd($this, $this->getFundingFrameworkForm());
    }

    protected function getFundingFrameworkForm() {
        $form = new Dnna_Form_AutoForm($this->view->classname, $this->view);
        // Προσθέτουμε τα επιχειρισιακά προγράμματα
        $formfield = new Dnna_Form_Abstract_FormField();
        $opprogrammessubform = new Dnna_Form_SubFormBase($this->view);
        for($i = 1; $i < $formfield->get_maxoccurs(); $i++) {
            $opprogrammessubform->addSubForm(new Dnna_Form_AutoForm('Application_Model_Lists_OpProgramme', $this->view, false, array($this->view->classname)), $i, false);
        }
        $opprogrammessubform->setLegend('Επιχειρισιακά Προγράμματα');
        $form->addSubForm($opprogrammessubform, 'opprogrammes');
        return $form;
    }
}
?>