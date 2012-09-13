<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_DeleteForm extends Dnna_Form_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url());

        $username = $this->addElement('text', 'deleteConfirm', array(
            'required'   => true,
            'label'      => 'Επιβεβαίωση Διαγραφής:',
        ));

        $login = $this->addElement('submit', 'delete', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Διαγραφή',
        ));
    }
}
?>