<?php
/**
 * @author Dimosthenis Nikoudis <dnna@dnna.gr>
 */
class Application_Form_LoginForm extends Dnna_Form_FormBase {
    public function init() {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAction($this->getView()->url(array(
            'controller' => 'Login',
            'action' => 'login'
        )));

        $username = $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('StringLength', false, array(3, 50)),
            ),
            'required'   => true,
            'label'      => 'Όνομα Χρήστη/Email:',
        ));

        $password = $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(6, 20)),
            ),
            'required'   => true,
            'label'      => 'Συνθηματικό:',
        ));

        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Σύνδεση',
        ));
        $loginDg = $this->addDisplayGroup(array('username', 'password', 'login'), 'loginForm', array('legend' => 'Αυθεντικοποίηση'));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'login_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
}
?>