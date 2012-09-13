<?php
class  Erga_Form_FullProject extends Erga_Form_Project {

    public function __construct($project, $view) {
        parent::__construct(null, $view, $project);
    }

    public function init() {
        parent::init();
        $employees = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_project->get_thisprojectemployees() as $curEmployee) {
            $employee = new Erga_Form_Apasxoloumenoi_Employee();
            $employee->set_empty(false);
            $employees->addSubForm($employee, $i++);
        }
        $this->addSubForm($employees, 'thisprojectemployees');

        $personnelcategoriesform = new Dnna_Form_SubFormBase($this->_view);
        $i = 1;
        foreach($this->_project->get_personnelcategories() as $curPersonnelcategory) {
            $cat = new Erga_Form_PersonnelCategories($this->_view);
            $catt = $cat->getSubForm('personnelcategories')->getSubForm($i);
            $catt->set_empty(false);
            $personnelcategoriesform->addSubForm($catt, $i++);
        }
        $this->addSubForm($personnelcategoriesform, 'personnelcategories');

        $subprojects = new Dnna_Form_SubFormBase();
        $i = 1;
        foreach($this->_project->get_subprojects() as $curSubproject) {
            $ypoergo = new Erga_Form_Ypoerga_FullYpoerga($curSubproject, $this->_view);
            $ypoergo->set_empty(false);
            $subprojects->addSubForm($ypoergo, $i++);
        }
        $this->addSubForm($subprojects, 'subprojects');
    }
}
?>