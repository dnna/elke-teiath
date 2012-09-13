<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Erga_Form_Ypoerga_FullYpoerga extends Erga_Form_Ypoerga_Ypoerga {

    public function __construct($subproject, $view = null) {
        parent::__construct(null, $view, $subproject);
    }

    public function init() {
        parent::init();
        $employees = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_subproject->get_employees() as $curEmployee) {
            $employee = new Erga_Form_Apasxoloumenoi_Employee();
            $employee->set_empty(false);
            $employees->addSubForm($employee, $i++);
        }
        $this->addSubForm($employees, 'employees');
        
        $contractors = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_subproject->get_contractors() as $curContractor) {
            $contractor = new Erga_Form_Apasxoloumenoi_Contractor();
            $contractor->set_empty(false);
            $contractors->addSubForm($contractor, $i++);
        }
        $this->addSubForm($contractors, 'contractors');

        $workpackages = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_subproject->get_workpackages() as $curWorkpackage) {
            $workpackage = new Erga_Form_Ypoerga_FullPaketoErgasias($curWorkpackage, $this->_view);
            $workpackage->set_empty(false);
            $workpackages->addSubForm($workpackage, $i++);
        }
        $this->addSubForm($workpackages, 'workpackages');
    }
}

?>