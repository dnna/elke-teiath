<?php
class  Erga_Form_FinancialDetails extends Dnna_Form_SubFormBase {
    protected $_project;

    public function __construct($view = null, $project = null) {
        $this->_project = $project;
        parent::__construct($view);
    }

    protected function indirectCosts() {
        // Άμεσες Δαπάνες
        $subform->addElement('text', 'directcosts', array(
            'label' => 'Άμεσες Δαπάνες:',
            'required' => true,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
            )
        );
        // Έμμεσες Δαπάνες
        $subform->addElement('text', 'indirectcosts', array(
            'label' => 'Έμμεσες Δαπάνες (%):',
            'required' => true,
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
            )
        );
        $subform->getElement('indirectcosts')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="indirectCostsFields">', 'placement' => 'prepend'));
        // Έμεσες Δαπάνες Ποσό
        $subform->addElement('text', 'indirectcostsamount', array(
            'label' => 'Ποσό Έμμεσων Δαπανών:',
            'readonly' => true,
            'ignore' => true,
            )
        );
        $subform->getElement('indirectcostsamount')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '</div>', 'placement' => 'append'));
    }

    protected function addFinancialDetailsFields(&$subform = null, $createSubform = true) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }
        // Πλαίσιο Χρηματοδότησης
        $fundingframeworksubform = new Dnna_Form_SubFormBase();
        $fundingframeworksubform->addElement('select', 'fundingframeworkid', array(
            'label' => 'Πλαίσιο Χρηματοδότησης:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_FundingFramework'),
            )
        );
        $subform->addSubForm($fundingframeworksubform, 'fundingframework', false);
        $subform->getSubForm('fundingframework')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="fundingFrameworkFields">', 'placement' => 'prepend'));
        // Επιχειρησιακό Πρόγραμμα
        $opprogrammesubform =  new Dnna_Form_SubFormBase();
        $opprogrammesubform->addElement('select', 'opprogrammeid', array(
            'label' => 'Ε.Π.:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_OpProgramme'),
            )
        );
        $subform->addSubForm($opprogrammesubform, 'opprogramme', false);

        $axissubform = new Dnna_Form_SubFormBase();
         // Αξονας
        $subform->addElement('text', 'axis', array(
            'label' => 'Άξονας:',
            )
        );
        // Κατηγορία
        $projectcategorysubform = new Dnna_Form_SubFormBase();
        $projectcategorysubform->addElement('select', 'id', array(
            'required' => true,
            'label' => 'Κατηγορία:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_ProjectCategory')
        ));
        $subform->addSubForm($projectcategorysubform, 'category', false);
        // Προϋπολογισμός Έργου
        $subform->addElement('text', 'budget', array(
            'label' => 'Προϋπολογισμός Έργου:',
            'required' => true,
            'validators' => array(
            array('validator' => 'Float')
            ),
            'class' => 'formatFloat',
        ));
        $subform->getElement('budget')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '<div class="budgetfields">', 'placement' => 'prepend'));
        // ΦΠΑ Προϋπολογισμού Έργου
        $subform->addElement('text', 'budgetfpa', array(
            'label' => 'ΦΠΑ:',
            'validators' => array(
                array('validator' => 'Float')
            ),
            'class' => 'formatFloat',

        ));
        // Σύνολο
        $subform->addElement('text', 'budgetwithfpa', array(
            'label' => 'Σύνολο (με ΦΠΑ):',
            'readonly' => true,
            'ignore' => true,
            'class' => 'formatFloat',

        ));
        $subform->getElement('budgetwithfpa')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '</div><div class="clearBoth"></div>', 'placement' => 'append'));
        $subform->addSubForm($axissubform, 'axissf', false);
        $subform->getSubForm('axissf')->addDecorator(array('groupDiv' => 'AnyMarkup'), array('markup' => '</div>', 'placement' => 'append'));
        // Ενάριθμος ΣΑΕ
        $subform->addElement('text', 'sae', array(
            'label' => 'Ενάριθμος ΣΑΕ:',
            )
        );
        // Εθνική Συμμετοχή
        $subform->addElement('text', 'nationalparticipation', array(
            'label' => 'Εθνική Συμμετοχή (%):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            //'class' => 'formatFloat',
        ));
        // Κοινοτική Συμμετοχή
        $subform->addElement('text', 'europeanparticipation', array(
            'label' => 'Κοινοτική Συμμετοχή (%):',
            'validators' => array(
                array('validator' => 'Float')
            ),
            //'class' => 'formatFloat',
        ));
        // Τράπεζα
        $banksubform = new Dnna_Form_SubFormBase();
        $banksubform->addElement('select', 'id', array(
            'required' => true,
            'label' => 'Τράπεζα:',
            'multiOptions' => Application_Model_Repositories_Lists::getListAsArray('Application_Model_Lists_Bank')
        ));
        $subform->addSubForm($banksubform, 'bank', false);
        // IBAN
        $subform->addElement('text', 'iban', array(
            'label' => 'IBAN:',
        ));
        // Λήξη Οικονομικού Αντικειμένου
        $subform->addElement('text', 'financialenddate', array(
            'label' => 'Λήξη Οικονομικού Αντικειμένου:',
            'validators' => array(
                array('validator' => 'Date')
            ),
            'class' => 'usedatepicker',
            'required' => true,
        ));

        // Αναλυτικός Προϋπολογισμός
        $budgetsubform = new Dnna_Form_SubFormBase();
        // Αντικείμενα 1-10
        for($i = 1; $i <= 10; $i++) {
            $budgetsubform->addSubForm(new Erga_Form_Subforms_BudgetItem(), $i, null, 'financialdetails-default-budgetitems');
            $budgetsubform->getSubForm($i)->setAttrib('class', 'tableSimRow');
        }

        $budgetsubform->addElement('text', 'sum', array(
            'label' => 'Σύνολο:',
            'readonly' => true,
            'ignore' => true
        ));
        $budgetsubform->getElement('sum')->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'tableSimRight'));
        $budgetsubform->getElement('sum')->addDecorator('AnyMarkup', array('markup' => '<div class="tableSimClear"></div>', 'placement' => 'append'));
        $budgetsubform->addElement('button', 'addBudgetItem', array(
            'label' => 'Προσθήκη Νέας Δαπάνης',
            'class' => 'budgetbuttons addButton',
        ));
        $subform->addSubForm($budgetsubform, 'budgetitems');
        $subform->getSubForm('budgetitems')->setLegend('Αναλυτικός Προϋπολογισμός');
        // Τέλος Αναλυτικού Προϋπολογισμού
        
        if($createSubform) {
            $this->addSubForm($subform, 'default');
        }
    }

    protected function addFundingReceiptFields(&$subform = null) {
        if($subform == null) {
            $subform = new Dnna_Form_SubFormBase();
        }
        // Αντικείμενα 1-10
        for($i = 1; $i <= 10; $i++) {
            $subform->addSubForm(new Erga_Form_Subforms_FundingReceipt($i), $i, null, 'financialdetails-fundingreceipts');
        }

        $subform->addElement('button', 'addFundingReceipt', array(
            'label' => 'Προσθήκη Χρηματοδότησης',
            'class' => 'fundingreceiptbuttons addButton',
        ));
        $this->addSubForm($subform, 'fundingreceipts');
        $this->getSubForm('fundingreceipts')->setLegend('Χρηματοδοτήσεις');
    }

    public function init() {
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/jquery.calculation.js', 'text/javascript'));
        $this->_view->headScript()->appendFile($this->_view->baseUrl('media/js/erga/projectfinancialdetails.js', 'text/javascript'));
        // financialdetailsid
        if($this->getElement('financialdetailsid') == null) {
            $this->addElement('hidden', 'financialdetailsid', array(
                'readonly' => true,
                )
            );
        }
        
        $this->addSubForm(new Application_Form_Subforms_AgencySelect('Φορέας Χρηματοδότησης', true, $this->_view), 'fundingagency');
        
        $this->addFinancialDetailsFields();
        
        $this->addFundingReceiptFields();
    }

    public function isValid($data) {
        $valid = parent::isValid($data);
        // Εξασφαλίζουμε ότι το επιχειρισιακό πρόγραμμα ανήκει στο πλαίσιο χρηματοδότησης
        $fundingframeworkid = $this->getSubForm('default')->getSubForm('fundingframework')->getElement('fundingframeworkid')->getValue();
        if($fundingframeworkid != null) {
            $fundingframework = Zend_Registry::get('entityManager')->getRepository('Application_Model_Lists_FundingFramework')->find($fundingframeworkid);
            if(!isset($fundingframework) || !$fundingframework instanceof Application_Model_Lists_FundingFramework) {
                throw new Exception('Υπήρξε σφάλμα κατά την ανάκτηση των στοιχείων του επιχειρισιακού προγράμματος');
            }
            $opprogrammeid = $this->getSubForm('default')->getSubForm('opprogramme')->getElement('opprogrammeid')->getValue();
            $opprogrammes = $fundingframework->get_opprogrammes();
            $found = false;
            foreach($opprogrammes as $curOpprogramme) {
                if($curOpprogramme->get_id() == $opprogrammeid) {
                    $found = true;
                    break;
                }
            }
            if(!$found) {
                $this->getSubForm('default')->getSubForm('opprogramme')->getElement('opprogrammeid')->addError('Το Επιχειρισιακό Πρόγραμμα πρέπει να ανήκει στο Πλαίσιο Χρηματοδότησης');
                $valid = false;
            }
        }
        return $valid;
    }
}
?>