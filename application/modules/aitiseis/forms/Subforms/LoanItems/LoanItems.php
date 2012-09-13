<?php

class Aitiseis_Form_Subforms_LoanItems_LoanItems extends Application_Form_AjaxSubFormBase {

    protected $_project;
    protected $_calledfromajax = false;

    public function __construct($params, $view = null) {
        parent::__construct($view);
    }

    public function setAjaxParams($params) {
        // Χρειάζεται να δεχόμαστε και projectid και project (το ένα είναι για ajax calls ενώ το άλλο για review)
        if (isset($params['projectid'])) {
            $this->_project = Zend_Registry::get('entityManager')->getRepository('Erga_Model_Project')->find($params['projectid']);
        } else if (isset($params['project'])) {
            $this->_project = $params['project'];
        }
    }

    public function ajaxInit() {
        $this->_calledfromajax = true;
        // Αντικείμενα
        if (isset($this->_project)) {
            $i = 1;
            foreach ($this->_project->get_financialdetails()->get_budgetitems() as $curBudgetItem) {
                $this->addSubForm(new Aitiseis_Form_Subforms_LoanItems_LoanItemBudgetItem($curBudgetItem, $this->_view), $i, false, 'loanitems');
                $i++;
            }

            $subform = new Dnna_Form_SubFormBase($this->_view);
            $i = 1;
            foreach ($this->_project->get_employees() as $curEmployee) {
                $subform->addSubForm(new Aitiseis_Form_Subforms_LoanItems_LoanItemEmployee($curEmployee, $this->_view), $i, false, 'loanitems');
                $i++;
            }
            $i = 1;
            foreach ($this->_project->get_contractors() as $curContractor) {
                $subform->addSubForm(new Aitiseis_Form_Subforms_LoanItems_LoanItemContractor($curContractor, $this->_view), $i, false, 'loanitems');
                $i++;
            }
            $subform->setLegend('Αμοιβές');
            $this->addSubForm($subform, 'default');
        }

        $this->addElement('text', 'sum', array(
            'label' => 'Σύνολο:',
            'readonly' => true,
            'ignore' => true
        ));
    }

    public function populate($object) {
        if($this->_calledfromajax == true) {
            if($this->getSubForm('default') != null) {
                self::populateForm($object, $this->getSubForm('default'));
            }
            return self::populateForm($object, $this);
        } else {
            return parent::populate($object);
        }
    }

    protected static function populateForm($object, Zend_Form $form) {
        if ($object instanceof Traversable || (is_array($object) && isset($object['1']))) {
            $i = 1;
            while(($subform = $form->getSubForm($i)) != null) {
                foreach ($object as $curObject) {
                    if ($subform instanceof Aitiseis_Form_Subforms_LoanItems_LoanItem &&
                         $curObject instanceof Aitiseis_Model_Daneismou_LoanItem &&
                         $subform->getAttachedObject() === $curObject->getAttachedObject()
                       ) {
                        $subform->populate($curObject);
                        if($subform->getElement('isvisible') != null) {
                            $subform->getElement('isvisible')->setValue('1');
                        }
                    }
                }
                $i++;
            }
        }
        //return $form->populate($object);
    }

    public function isValid($data) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $this->setAjaxParams($params['project']); // Για το projectid
        $this->ajaxInit();
        foreach ($data['default'] as &$curItem) {
            self::fixIsVisible($curItem);
        }
        foreach ($data as &$curItem) {
            self::fixIsVisible($curItem);
        }
        return parent::isValid($data);
    }
    
    protected static function fixIsVisible(&$curItem) {
        if (isset($curItem['amount'])) {
            if ($curItem['amount'] != '') {
                $curItem['isvisible'] = '1';
            } else {
                $curItem['isvisible'] = '0';
            }
        }
    }

}
?>