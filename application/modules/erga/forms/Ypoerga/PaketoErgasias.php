<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Ypoerga_PaketoErgasias extends Erga_Form_Ypoerga_FormBase {
    /**
     * @var Erga_Model_SubItems_WorkPackage 
     */
    protected $_workpackage;
    
    public function __construct($view = null, $workpackage = null) {
        $this->_workpackage = $workpackage;
        parent::__construct($view);
    }

    protected function addWorkPackageFields(&$subform = null) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }

        // Κωδικός
        $subform->addElement('text', 'workpackagecodename', array(
            'label' => 'Κωδικός Πακέτου Εργασίας:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 10))
            ),
            'required' => true,
            )
        );
        // Τίτλος
        $subform->addElement('textarea', 'workpackagename', array(
            'label' => 'Τίτλος Πακέτου Εργασίας:',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, $this->_textareaMaxLength))
            ),
            'rows' => 1,
            'cols' => $this->_textareaCols,
            'required' => true,
            )
        );
    }

    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/paketaergasias/paketaergasias.js', 'text/javascript'));

        /*$subprojectform = new Erga_Form_Subforms_ParentSubProjectFields($this->_view);
        $subprojectform->setLegend('Στοιχεία Υποέργου');
        if($this->_view->getProject() == null || $this->_view->getProject()->get_iscomplex() == 1) {
            $subprojectform->setLegend('Στοιχεία Υποέργου');
        }
        $this->addSubForm($subprojectform, 'subproject');*/

        $subform = new Dnna_Form_SubFormBase();
        $this->addWorkPackageFields($subform);
        $subform->setLegend('Στοιχεία Πακέτου Εργασίας');
        $this->addSubForm($subform, 'default');

        $this->addSubmitFields();
    }
}

?>