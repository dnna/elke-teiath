<?php
class  Erga_Form_SubprojectsName extends Dnna_Form_FormBase {
    public function init() {
        $subprojectnames = Erga_Model_Project::getSubProjectNames();
        $options = array();
        foreach($subprojectnames as $curNum => $curName) {
            $options[$curNum] = $curName['namepl'];
        }
        $this->addElement('select', 'subprojectsname', array(
            //'label' => 'Νέα ονομασία',
            'required' => true,
            'multiOptions' => $options
        ));
    }
}
?>