<?php
class Timesheets_Form_TemplateSelect extends Dnna_Form_FormBase {
    protected $_mytimesheet;

    public function __construct($view = null, $mytimesheet = false) {
        $this->_mytimesheet = $mytimesheet;
        parent::__construct($view);
    }

    public function init() {
        $this->setMethod('GET');
        $auth = Zend_Auth::getInstance();
        if($this->_mytimesheet == true) {
            $authuser = Zend_Auth::getInstance()->getStorage()->read();
            $entries = array();
            foreach($authuser->get_contracts() as $curContract) {
                $entries[$curContract->get_recordid()] = $curContract->getProjectName().' '.$curContract->get_startdate().'–'.$curContract->get_enddate();
            }
            $this->addElement('select', 'employee', array(
                'label' =>  'Σύμβαση: ',
                'required'  =>  true,
                'multiOptions'  => $entries,
            ));
        } else {
            $this->addSubForm(new Application_Form_Subforms_ProjectSelect(array('required' => true), $this->_view), 'project', false);
            if($auth->hasIdentity() && ($auth->getStorage()->read()->hasRole('elke') || $auth->getStorage()->read()->hasRole('professor'))) {
                $this->addElement('select', 'employee', array(
                    'label' =>  'Σύμβαση: ',
                    'required'  =>  true,
                    'multiOptions'  => array('Επιλέξτε έργο'),
                    //'value' =>  blabla // TODO Αν ο χρήστης ΕΛΚΕ είναι ταυτόχρονα και απασχολούμενος σε κάποιο έργο τότε να τον επιλέγει σαν default
                ));
                $this->getElement('employee')->setRegisterInArrayValidator(false);
            }
        }
        $this->addElement('select', 'month', array(
            'label' =>  'Μήνας: ',
            'required'  =>  true,
            'multiOptions'  => array_combine(range(1, 12), range(1, 12)),
            'value' =>  (int)date('n'),
        ));
        $this->addElement('select', 'year', array(
            'label' =>  'Έτος: ',
            'required'  =>  true,
            'multiOptions'  =>  array_combine(range((int)date('Y')-20, (int)date('Y')+5), range((int)date('Y')-20, (int)date('Y')+5)),
            'value' =>  (int)date('Y'),
        ));
        $this->addElement('submit', 'submit', array(
            'label' =>  'Λήψη Αρχείου',
        ));
    }
}

?>